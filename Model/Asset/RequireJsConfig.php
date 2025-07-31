<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Asset;

use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\View\Asset\Minification;
use Magento\Framework\View\Asset\RepositoryMap;
use Magento\Framework\RequireJs\Config\File\Collector\Aggregated;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Code\Minifier\AdapterInterface;
use Magento\Framework\RequireJs\Config;

/**
 * Generate RequireJs configuration file
 */
class RequireJsConfig extends Config
{
    /**
     * @var Aggregated
     */
    private $fileSource;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var \Magento\Framework\View\Asset\ContextInterface
     */
    private $staticContext;

    /**
     * @var AdapterInterface
     */
    private $minifyAdapter;

    /**
     * @var Minification
     */
    private $minification;

    /**
     * @var RepositoryMap
     */
    private $repositoryMap;

    /**
     * @var bool
     */
    private $forceUnminified = false;

    /**
     * @param Aggregated $fileSource
     * @param DesignInterface $design
     * @param ReadFactory $readFactory
     * @param Repository $assetRepo
     * @param AdapterInterface $minifyAdapter
     * @param Minification $minification
     * @param RepositoryMap $repositoryMap
     */
    public function __construct(
        Aggregated $fileSource,
        DesignInterface $design,
        ReadFactory $readFactory,
        Repository $assetRepo,
        AdapterInterface $minifyAdapter,
        Minification $minification,
        RepositoryMap $repositoryMap
    ) {
        $this->fileSource = $fileSource;
        $this->design = $design;
        $this->readFactory = $readFactory;
        $this->staticContext = $assetRepo->getStaticViewFileContext();
        $this->minifyAdapter = $minifyAdapter;
        $this->minification = $minification;
        $this->repositoryMap = $repositoryMap;
    }

    /**
     * Get aggregated distributed configuration
     *
     * @return string
     */
    public function getConfig()
    {
        $distributedConfig = '';
        $customConfigFiles = $this->fileSource->getFiles($this->design->getDesignTheme(), self::CONFIG_FILE_NAME);

        foreach ($customConfigFiles as $file) {
            /** @var $fileReader \Magento\Framework\Filesystem\File\Read */
            $fileReader = $this->readFactory->create($file->getFilename(), DriverPool::FILE);
            $config = $fileReader->readAll($file->getName());
            $distributedConfig .= str_replace('%config%', $config, self::PARTIAL_CONFIG_TEMPLATE);
        }

        $fullConfig = str_replace(['%function%', '%usages%'], [$distributedConfig], self::FULL_CONFIG_TEMPLATE);

        if ($this->minification->isEnabled('js') && !$this->forceUnminified) {
            $fullConfig = $this->minifyAdapter->minify($fullConfig);
        }

        return $fullConfig;
    }

    /**
     * Get path to config file relative to directory, where all config files with different context are located
     *
     * @return string
     */
    public function getConfigFileRelativePath()
    {
        return $this->staticContext->getConfigPath() . '/' . $this->getConfigFileName();
    }

    /**
     * Get path to config file relative to directory, where all config files with different context are located
     *
     * @return string
     */
    public function getMixinsFileRelativePath()
    {
        $map = $this->getRepositoryFilesMap(
            Config::MIXINS_FILE_NAME,
            [
                'area' => $this->staticContext->getAreaCode(),
                'theme' => $this->staticContext->getThemePath(),
                'locale' => $this->staticContext->getLocale(),
            ]
        );
        if ($map) {
            $relativePath = implode('/', $map) . '/' . Config::MIXINS_FILE_NAME;
        } else {
            $relativePath = $this->staticContext->getPath() . '/' . self::MIXINS_FILE_NAME;
        }
        return $relativePath;
    }

    /**
     * Get path to config file relative to directory, where all config files with different context are located
     *
     * @return string
     */
    public function getRequireJsFileRelativePath()
    {
        return $this->staticContext->getConfigPath() . '/' . self::REQUIRE_JS_FILE_NAME;
    }

    /**
     * Get base RequireJs configuration necessary for working with Magento application
     *
     * @return string
     */
    public function getBaseConfig()
    {
        $config = [
            'baseUrl' => $this->staticContext->getBaseUrl() . $this->staticContext->getPath(),
        ];
        $config = json_encode($config, JSON_UNESCAPED_SLASHES);
        $result = "require.config($config);";
        return $result;
    }

    /**
     * Get path to '.min' files resolver relative to config files directory
     *
     * @return string
     */
    public function getMinResolverRelativePath()
    {
        return
            $this->staticContext->getConfigPath() .
            '/' .
            $this->minification->addMinifiedSign(self::MIN_RESOLVER_FILENAME);
    }

    /**
     * Get path to URL map resover file
     *
     * @return string
     */
    public function getUrlResolverFileRelativePath()
    {
        $map = $this->getRepositoryFilesMap(
            Config::URL_RESOLVER_FILE_NAME,
            [
                'area' => $this->staticContext->getAreaCode(),
                'theme' => $this->staticContext->getThemePath(),
                'locale' => $this->staticContext->getLocale(),
            ]
        );
        if ($map) {
            $relativePath = implode('/', $map) . '/' . Config::URL_RESOLVER_FILE_NAME;
        } else {
            $relativePath = $this->staticContext->getPath() . '/' . self::URL_RESOLVER_FILE_NAME;
        }
        return $relativePath;
    }

    /**
     * Get path to map file
     *
     * @return string
     */
    public function getMapFileRelativePath()
    {
        return $this->minification->addMinifiedSign($this->staticContext->getPath() . '/' . self::MAP_FILE_NAME);
    }

    /**
     * Get path to configuration file
     *
     * @return string
     */
    protected function getConfigFileName()
    {
        return $this->minification->addMinifiedSign(self::CONFIG_FILE_NAME);
    }

    /**
     * Get resolver code which RequireJS fetch minified files instead
     *
     * @return string
     */
    public function getMinResolverCode()
    {
        $excludes = ['url.indexOf(baseUrl)===0'];
        foreach ($this->minification->getExcludes('js') as $expression) {
            $excludes[] = '!url.match(/' . str_replace('/', '\/', $expression) . '/)';
        }
        $excludesCode = empty($excludes) ? 'true' : implode('&&', $excludes);

        $result = <<<code
    (function () {
        var ctx = require.s.contexts._,
            origNameToUrl = ctx.nameToUrl,
            baseUrl = ctx.config.baseUrl;

        ctx.nameToUrl = function() {
            var url = origNameToUrl.apply(ctx, arguments);
            if ({$excludesCode}) {
                url = url.replace(/(\.min)?\.js$/, '.min.js');
            }
            return url;
        };
    })();
code;

        if ($this->minification->isEnabled('js') && !$this->forceUnminified) {
            $result = $this->minifyAdapter->minify($result);
        }
        return $result;
    }

    /**
     * Get map for given file.
     *
     * @param string $fileId
     * @param array $params
     * @return array
     */
    private function getRepositoryFilesMap($fileId, array $params)
    {
        return $this->repositoryMap->getMap($fileId, $params);
    }

    /**
     * Force unminified version of the configuration
     * 
     * @param bool $forceUnminified
     */
    public function setForceUnminified(bool $forceUnminified)
    {
        $this->forceUnminified = $forceUnminified;
    }
}
