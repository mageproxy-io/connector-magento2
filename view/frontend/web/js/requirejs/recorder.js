/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

define('mageproxy/requirejs-recorder', [ 'module' ], function (module) {
    'use strict';

    const { _: defaultCtx } = require.s.contexts;
    const { config: defaultCtxConfig } = defaultCtx;
    const moduleConfig = module.config && module.config() || {};
    const pixelPool = new Set();

    require.config({
        onNodeCreated: function (node, config, moduleName, url) { // SRI

            // Covers Magento 2.4.7 and above implementation
            if ('sriHashes' in window && url in window.sriHashes) {
                node.setAttribute('integrity', window.sriHashes[url]);
                node.setAttribute('crossorigin', 'anonymous');
            }

            // Covers mageproxy bundles SRI
            if ('sriHashes' in moduleConfig && url in moduleConfig.sriHashes) {
                node.setAttribute('integrity', moduleConfig.sriHashes[url]);
                node.setAttribute('crossorigin', 'anonymous');
            }
        }
    });

    /**
     * We are in bundling mode, and we want to make sure that any missing dependencies
     * can be retrieved from the origin host because they might not be in the bundles,
     * since the bundles are only as good as the recording
     * This function is the only function that is called consistently across
     * normal js dependencies and plugins like text!
     */
    function registerFallback() {

        if (!moduleConfig.optimized) {
            return;
        }

        const bundleRegex = /bundles\/[0-9a-z\.]+(\.min)?(\.js)?$/;
        const nameToUrl = defaultCtx.nameToUrl;
        const staticPathRegex = /\/static\/version\d+/;
        const minifiedUrlRegex = /\.min\.js$/;
        const origBaseUrl = moduleConfig.origBaseUrl;
        const { pathname: origBaseUrlPath } = new URL(origBaseUrl);
        defaultCtx.nameToUrl = function () {
            let url = nameToUrl.apply(defaultCtx, arguments);
            if (/^(https?)?:?\/\//.test(arguments[0])) {
                // nameToUrl argument starts with  http://, https:// or //, return the url as is
                // since it's an external resource
                return url;
            }
            if (url.startsWith('/')) {
                // nameToUrl result starts with a slash, return the url as is
                return url;
            }
            const [ moduleName ] = Array.from(arguments);
            if (bundleRegex.test(moduleName) || bundleRegex.test(url)) {
                // check if the bundle is minified based on URL
                if (minifiedUrlRegex.test(url)) {
                    // if the URL is for a minified bundle, but minification is not enabled, revert to the original URL
                    // without minification
                    return moduleConfig.minified ? url : url.replace(/\.min\.js$/, '.js');
                }
                return moduleConfig.minified ? url.replace(/(\.min)?\.js$/, '.min.js') : url;
            } else if ([ 'require', 'module', 'exports' ].includes(moduleName) || /^_@r\d+$/.test(moduleName)) {
                return url;
            }

            // The URL is pointing to a completely different origin, return the url as is
            // This wouldn't have been caught by the regex check above because the URL
            // because it's possible to first map a module to a path that ends up pointing
            // to an external URL
            if ((new URL(defaultCtxConfig.baseUrl)).origin !== (new URL(url)).origin) {
                return url;
            }

            // Request for a resource that is not bundled, fallback to the original domain
            const nameToUrlResultPathExOpt = url.slice(url.search(staticPathRegex));
            return origBaseUrl.replace(origBaseUrlPath, nameToUrlResultPathExOpt);
        }
    }

    function register() {

        if (getRecordingIdFromCookie() === null) {
            return;
        }

        const recorderContext = require.s.newContext('recorder');
        recorderContext.init = true;

        /**
         * Build query parameter payload for request to the mageproxy recorder
         *
         * @param moduleId - RequireJS moduleID
         * @param {string} sft - Static File Target
         * @returns {Object}
         */
        function urlArgs(moduleId, sft) {
            const args = {};

            args['mpx_m'] = moduleId;
            args['mpx_h'] = moduleConfig.pageHandle;
            args['mpx_r'] = getRecordingIdFromCookie();

            if (moduleConfig.includeTs) {
                const ts = (new Date()).getTime();
                args['mpx_t'] = ts.toString();
            }
            args['mpx_sft'] = btoa(sft);
            return args;
        }

        const trackQueue = [];
        let flushScheduled = false;
        let worker = null;

        function scheduleFlush() {
            if (flushScheduled) { return; }
            flushScheduled = true;
            const run = function () {
                flushScheduled = false;
                flushQueue();
            };
            if ('requestIdleCallback' in window) {
                try { window.requestIdleCallback(run, { timeout: 2000 }); return; } catch (e) {}
            }
            setTimeout(run, 0);
        }

        function ensureWorker() {
            if (worker !== null) { return worker; }
            try {
                // Inline worker to perform fetch requests off the main thread
                var code = [
                    'self.onmessage = function(e){',
                    '  var urls = (e && e.data && e.data.urls) ? e.data.urls : (Array.isArray(e && e.data) ? e.data : []);',
                    '  var MAX = 6;',
                    '  var inFlight = 0, idx = 0;',
                    '  function pump(){',
                    '    if (idx >= urls.length) { return; }',
                    '    while (inFlight < MAX && idx < urls.length) {',
                    '      var u = urls[idx++];',
                    '      inFlight++;',
                    '      try {',
                    "        fetch(u, { method: 'GET', mode: 'cors', credentials: 'omit' })",
                    "          .then(function(){}, function(){})",
                    "          .then(function(){ inFlight--; pump(); });",
                    '      } catch (ex) {',
                    '        inFlight--;',
                    '        pump();',
                    '      }',
                    '    }',
                    '  }',
                    '  pump();',
                    '};'
                ].join('\n');
                var blob = new Blob([ code ], { type: 'text/javascript' });
                var wurl = URL.createObjectURL(blob);
                worker = new Worker(wurl);
                try { URL.revokeObjectURL(wurl); } catch (e) {}
            } catch (e) {
                worker = null;
            }
            return worker;
        }

        function flushQueue() {
            if (!trackQueue.length) { return; }
            const batch = trackQueue.splice(0, trackQueue.length);
            const w = ensureWorker();
            if (w) {
                try { w.postMessage({ urls: batch }); return; } catch (e) {}
            }
            // Fallback on main thread: prefer fetch (CORS) to avoid image decode/layout
            for (let i = 0; i < batch.length; i++) {
                try {
                    if ('fetch' in window) {
                        fetch(batch[i], { method: 'GET', mode: 'cors', credentials: 'omit' }).catch(function(){});
                    } else {
                        addTrackingPixel(batch[i]);
                    }
                } catch (e) {
                    // Final fallback
                    addTrackingPixel(batch[i]);
                }
            }
        }

        function enqueueTrackUrl(url) {
            trackQueue.push(url);
            scheduleFlush();
        }

        // Ensure pending queue is flushed on page hide/unload
        try {
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'hidden') { flushQueue(); }
            }, { capture: true, passive: true });
            window.addEventListener('pagehide', function () { flushQueue(); }, { capture: true, passive: true });
        } catch (e) {}

        /**
         * Tracks a dependency against the mageproxy recorder (non-blocking)
         * @param args
         */
        function track(args) {
            var url = new URL(moduleConfig.trackUrl);
            for (const [ key, value ] of Object.entries(args)) {
                url.searchParams.append(key, value);
            }
            enqueueTrackUrl(url.toString());
        }

        // Retained for legacy fallback and compatibility when fetch/worker unavailable
        function addTrackingPixel(url) {
            const img = new Image();
            if ('decoding' in img) { img.decoding = 'async'; }
            if ('fetchPriority' in img) { img.fetchPriority = 'low'; }
            if ('crossOrigin' in img) { img.crossOrigin = 'anonymous'; }
            else { try { img.setAttribute('crossorigin', 'anonymous'); } catch (e) {} }
            // Do not force a no-referrer policy; allow default/referrer for auditing
            const cleanup = () => { pixelPool.delete(img); };
            img.onload = cleanup; img.onerror = cleanup; img.onabort = cleanup;
            pixelPool.add(img);
            img.src = url;
        }

        /**
         * These dependencies are the backbone of the Magento RequireJs
         * implementation. However, they are not loaded via RequireJS,
         * and we do need to record them to create an optimized build
         * hence we are recording them explicitly because they wont pass
         * through the RequireJs.onResourceLoad hook
         */
        function recordNonAmd() {
            for (const [ moduleId, sft ] of Object.entries(moduleConfig.nonAmd)) {
                const args = urlArgs(moduleId, sft);
                // Queue instead of immediate network to avoid blocking
                track(args);
            }
        }

        /**
         * "onResourceLoad" is only listened to when we are in recording mode.
         * This is our hook to call the recorder api...
         * It's called by RequireJs after a module has been loaded.
         * It will only be called once for any given resource and will also be invoked
         * for each dependency even when they are bundled!
         */
        require.onResourceLoad = function handler(context, map, depArray) {

            if (handler.invokeCnt === undefined) {
                handler.invokeCnt = 0;
            }

            if (map.prefix && map.prefix !== 'text'
                || map.id === 'mixins'
                || map.id === 'mageproxy/requirejs-recorder'
                || map.id.startsWith('core-js/modules')
                || map.id.startsWith('core-js/internals')
                || map.id.startsWith('/')
                || (map.url && !map.url.startsWith(defaultCtxConfig.baseUrl)
                    && !map.url.startsWith(moduleConfig.origBaseUrl))
            ) {
                // map.prefix denotes use of a RequireJs plugin
                // Any plugin other than text! (e.g. mixins!, domReady!) can be ignored
                return;
            }

            if (!map.prefix && !(map.url in context.urlFetched)) {
                // inline defined modules are not fetched from the server
                return;
            }

            if (recorderContext.init) {
                configureRecordingContext(recorderContext);
            }

            handler.invokeCnt++;

            const sft = recorderContext.nameToUrl(map.name, null, !!map.prefix);

            const args = urlArgs(map.id, sft);
            track(args);
        }

        function configureRecordingContext(context) {
            context.configure({
                baseUrl: !!moduleConfig.optimized ? moduleConfig.origBaseUrl : defaultCtxConfig.baseUrl,
                paths: defaultCtxConfig.paths,
                map: defaultCtxConfig.map,
                shim: defaultCtxConfig.shim,
                config: defaultCtxConfig.config
            });
            delete context.init;
        }

        recordNonAmd();
    }

    function getRecordingIdFromCookie() {
        const cookies = document.cookie.split('; ');
        for (const cookie of cookies) {
            const [ key, value ] = cookie.split('=');
            if (key === 'mageproxy_recording') {
                return decodeURIComponent(value); // Decode and return the value
            }
        }
        return null;
    }

    return {
        register,
        registerFallback
    };

});

require([ 'mageproxy/requirejs-recorder' ], function (recorder) {
    recorder.register();
    recorder.registerFallback();
});
