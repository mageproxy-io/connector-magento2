<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\View\Deployment\Version;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\AutoRunScheduleFactory;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\ConfigValidationFailedFlag;
use Mageproxy\Connector\Model\PurgeFullPageCache;
use Mageproxy\Connector\Model\System\Config\Source\RunMode;

class StartNewRecordings
{
    private Version $deployVersion;
    private RecordingRepositoryInterface $recordingRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private RecordingManagerInterface $recordingManager;
    private StoreManagerInterface $storeManager;
    private Config $config;
    private PurgeFullPageCache $purgeFullPageCache;
    private ConfigValidationFailedFlag $validationFailedFlag;
    private AutoRunScheduleFactory $autoRunScheduleFactory;

    public function __construct(
        Version $deployVersion,
        RecordingRepositoryInterface $recordingRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RecordingManagerInterface $recordingManager,
        StoreManagerInterface $storeManager,
        Config $config,
        PurgeFullPageCache $purgeFullPageCache,
        ConfigValidationFailedFlag $validationFailedFlag,
        AutoRunScheduleFactory $autoRunScheduleFactory
    ) {
        $this->deployVersion = $deployVersion;
        $this->recordingRepository = $recordingRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->recordingManager = $recordingManager;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->purgeFullPageCache = $purgeFullPageCache;
        $this->validationFailedFlag = $validationFailedFlag;
        $this->autoRunScheduleFactory = $autoRunScheduleFactory;
    }

    public function execute(): void
    {
        if (!$this->config->getIsEnabled()
            || $this->validationFailedFlag->has()
        ) {
            return;
        }

        $version = $this->deployVersion->getValue();

        // Get recordings for currently deployed static version
        // and initiated by the cron
        // User initiated recordings will be finished by another cron
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('static_version', $version)
            ->addFilter('initiator', RecordingInterface::INITIATOR_CRON)
            ->create();

        $items = $this->recordingRepository->getList($searchCriteria)->getItems();

        $storeIds = array_diff(
            $this->getActiveStoreIds(), // active stores
            array_map(function (RecordingInterface $recording) { // stores with recordings
                return (int) $recording->getStoreId();
            }, $items)
        );

        // Only automatically start recordings for those stores that have the feature enabled
        $storeIds = array_filter($storeIds, function ($storeId) {
            return $this->config->getRunMode($storeId) === RunMode::MODE_AUTO;
        });

        if (!empty($storeIds)) {
            $this->recordingManager->createNewRecordings($storeIds);
            $this->purgeFullPageCache->execute();
        }
    }

    private function getActiveStoreIds(): array
    {
        return array_reduce($this->storeManager->getStores(), function ($acc, Store $store) {
            if ($store->isActive()) {
                $acc[] = (int) $store->getId();
            }
            return $acc;
        }, []);
    }
}
