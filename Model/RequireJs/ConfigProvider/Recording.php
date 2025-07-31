<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\RequireJs\ConfigProvider;

use Magento\Framework\App\Request\Http;
use Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Model\Asset\AssetRegistry;
use Mageproxy\Connector\Model\Config;

class Recording implements RequireJsConfigProviderInterface
{
    private RecordingManagerInterface $recordingManager;
    private Http $request;
    private Config $config;
    private AssetRegistry $nonAmd;

    public function __construct(
        RecordingManagerInterface $recordingManager,
        Http $request,
        Config $config,
        AssetRegistry $nonAmd
    ) {
        $this->recordingManager = $recordingManager;
        $this->request = $request;
        $this->config = $config;
        $this->nonAmd = $nonAmd;
    }

    public function getConfig(): array
    {
        return [
            'config' => [
                self::REQUIREJS_MODULE_CONFIG_KEY => [
                    'trackUrl' => $this->recordingManager->getTrackingUrl(),
                    'pageHandle' => $this->request->getFullActionName(),
                    'includeTs' => $this->config->getIncludeTimestamp(),
                    'nonAmd' => $this->getNonAmd()
                ]
            ]
        ];
    }

    private function getNonAmd(): array
    {
        $nonAmd = [];
        $assets = $this->nonAmd->registry();
        foreach ($assets as $moduleId => $asset) {
            $nonAmd[$moduleId] = $asset->getUrl();
        }
        // Remove any '.min' extension from the URLs
        return array_map(function ($url) {
            return preg_replace('/\.min\./', '.', $url);
        }, $nonAmd);
    }
}
