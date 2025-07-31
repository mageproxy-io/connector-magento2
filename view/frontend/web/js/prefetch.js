/*
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

define(['underscore', 'jquery', 'jquery/jquery-storageapi'], function (_, $) {


    // Registry to avoid dupes
    var sessionStorageKey = 'prefetched-bundles',
        storage = $.initNamespaceStorage('mageproxy').sessionStorage;

    /**
     * Dynamically add prefetch links for the given bundles in head section
     *
     * @param {Array} bundles
     */
    function prefetchBundles(bundles) {
        var prefetched = storage.get(sessionStorageKey) || [];
        _.each(bundles, function (bundleUrl) {
            if (!_.contains(prefetched, bundleUrl)) {
                prefetched.push(bundleUrl);
                storage.set(sessionStorageKey, prefetched);
                $('<link>', {
                    rel: 'prefetch',
                    href: bundleUrl,
                    as: 'script'
                }).appendTo('head');
            }
        });
    }

    return function (config) {
        _.each(config, function (info, selector) {
            var bundles = info.bundles || [],
                prefetchOn = info.prefetchOn || 'interaction',
                observer;

            if (prefetchOn === 'viewport' && 'IntersectionObserver' in window) {
                observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            prefetchBundles(bundles);
                            observer.unobserve(entry.target); // Stop observing after prefetch
                        }
                    });
                });

                $(selector).each(function () {
                    observer.observe(this);
                });
            } else {
                $(document).on('mouseenter focus touchstart', selector, function () {
                    prefetchBundles(bundles);
                });
            }
        });
    }
});
