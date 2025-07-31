<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Provider;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Model\Provider\RecordingsProvider;
use Mageproxy\Connector\Model\Provider\SearchCriteriaProvider;
use PHPUnit\Framework\TestCase;

class RecordingsProviderTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recordings.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testItReturnsACollectionOfRecordingsWithTheGivenStatuses(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $searchCriteriaProvider = $objectManager->create(SearchCriteriaProvider::class, [
            'statuses' => [ RecordingInterface::STATUS_PENDING, RecordingInterface::STATUS_RUNNING ]
        ]);

        $provider = $objectManager->create(RecordingsProvider::class, [
            'searchCriteriaProvider' => $searchCriteriaProvider
        ]);
        self::assertCount(2, $provider->getItems());
        foreach ($provider->getItems() as $recording) {
            if (!in_array($recording->getStatus(), [0, 1])) {
                self::fail('Recording should either be pending or running.');
            }
        }
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recordings.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testEmptyStatusesReturnsAllRecordings(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $provider = $objectManager->create(RecordingsProvider::class);
        self::assertCount(6, $provider->getItems());
    }
}
