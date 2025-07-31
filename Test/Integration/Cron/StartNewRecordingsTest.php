<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Cron\StartNewRecordings;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingResponseInterface;
use Mageproxy\Connector\Model\PurgeFullPageCache;
use PHPUnit\Framework\TestCase;

class StartNewRecordingsTest extends TestCase
{
    /**
     * @magentoAppArea crontab
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoConfigFixture default_store mageproxy_connector/settings/run_mode auto
     * @magentoConfigFixture default_store mageproxy_connector/settings/auto_run_type scheduled
     */
    public function testItStartsNewRecordings(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        // Set configuration value
        $objectManager->get(MutableScopeConfigInterface::class)->setValue(
            'mageproxy_connector/settings/record_schedule',
            json_encode([
                [
                    'record_for' => 5,
                    'record_time_unit' => 'm',
                    'pause_for' => 5,
                    'pause_time_unit' => 'm'
                ]
            ]),
            ScopeInterface::SCOPE_STORE,
            'default'
        );

        $cachePurgerMock = self::createMock(PurgeFullPageCache::class);
        $cachePurgerMock->expects(self::once())
            ->method('execute');

        $postNewRecordingResult = $objectManager->create(PostNewRecordingResponseInterface::class);
        $postNewRecordingResult->setId('123');
        $postNewRecordingMock = self::createMock(PostNewRecordingInterface::class);
        $postNewRecordingMock
            ->method('execute')
            ->willReturn($postNewRecordingResult);

        $objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);
        $objectManager->addSharedInstance($cachePurgerMock, PurgeFullPageCache::class, true);

        $cron = $objectManager->get(StartNewRecordings::class);
        $cron->execute();
        $recordingRepository = $objectManager->create(RecordingRepositoryInterface::class);
        $searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
        $result = $recordingRepository->getList($searchCriteriaBuilder->create());
        self::assertSame(1, $result->getTotalCount());
    }
}
