<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\RequireJs\ConfigProvider;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Asset\File;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\Asset\AssetRegistry;
use Mageproxy\Connector\Model\RequireJs\ConfigProvider\Recording;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Model\RequireJs\Recording
 */
class RecordingTest extends TestCase
{

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     * @magentoConfigFixture default/mageproxy_connector/settings/tracking_url https://recorder.example.com/track
     */
    public function testResult(): void
    {
        /** @var \Magento\Framework\ObjectManager\ $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $recording = $objectManager->get(RecordingRepositoryInterface::class)->get('running');

        // Setup the request handle
        $request = $objectManager->get(Http::class);
        $request->setRouteName('cms')->setControllerName('index')->setActionName('index');

        $nonAmdRegistry = self::createMock(AssetRegistry::class);
        $returnValue = [];
        foreach(['http://www.example.com/foo.js', 'http://www.example.com/bar.js'] as $url) {
            $assetMock = self::createMock(File::class);
            $assetMock->expects(self::once())
                ->method('getUrl')
                ->willReturn($url);
            $returnValue['module/' . basename($url, '.js')] = $assetMock;
        }
        $nonAmdRegistry->expects(self::once())
            ->method('registry')
            ->willReturn($returnValue);

        $configProvider = $objectManager->create(Recording::class, [
            'nonAmd' => $nonAmdRegistry
        ]);

        $result = $configProvider->getConfig();

        $expected = [
            'config' => [
                'mageproxy/requirejs-recorder' => [
                    'trackUrl' => 'https://recorder.example.com/track',
                    'pageHandle' => 'cms_index_index',
                    'includeTs' => $recording->getIncludeTimestamp(),
                    'nonAmd' => [
                        'module/foo' => 'http://www.example.com/foo.js',
                        'module/bar' => 'http://www.example.com/bar.js'
                    ]
                ]
            ]
        ];
        self::assertSame($expected, $result);
    }
}
