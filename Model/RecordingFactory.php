<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\App\View\Deployment\Version;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\Data\RecordingInterfaceFactory;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingRequestInterfaceFactory;
use Mageproxy\Connector\Model\System\Config\Source\AutoRunType;
use Mageproxy\Connector\Model\System\Config\Source\RunMode;

class RecordingFactory
{
    private State $appState;
    private RecordingInterfaceFactory $recordingFactory;
    private DateTime $dateTimeFmt;
    private PostNewRecordingRequestInterfaceFactory $postNewRecordingRequestFactory;
    private PostNewRecordingInterface $postNewRecording;
    private Config $config;
    private AutoRunScheduleFactory $autoRunScheduleFactory;
    private StoreManagerInterface $storeManager;
    private Version $deployedVersion;

    public function __construct(
        State $appState,
        RecordingInterfaceFactory $recordingFactory,
        DateTime $dateTimeFmt,
        PostNewRecordingRequestInterfaceFactory $postNewRecordingRequestFactory,
        PostNewRecordingInterface $postNewRecording,
        Config $config,
        AutoRunScheduleFactory $autoRunScheduleFactory,
        StoreManagerInterface $storeManager,
        Version $deployedVersion
    ) {
        $this->appState = $appState;
        $this->recordingFactory = $recordingFactory;
        $this->dateTimeFmt = $dateTimeFmt;
        $this->postNewRecordingRequestFactory = $postNewRecordingRequestFactory;
        $this->postNewRecording = $postNewRecording;
        $this->config = $config;
        $this->autoRunScheduleFactory = $autoRunScheduleFactory;
        $this->storeManager = $storeManager;
        $this->deployedVersion = $deployedVersion;
    }

    /**
     * Create a recording object hydrated with some of that mageproxy API goodness..
     */
    public function create(
        int $storeId,
        string $runMode,
        ?int $duration = null,
        ?int $scheduledAtTs = null): RecordingInterface
    {
        $request = $this->postNewRecordingRequestFactory->create();
        $request->setServiceId($this->config->getServiceId());
        $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();

        // Send the domain for the requested recording
        if (class_exists('\Laminas\Uri\UriFactory')) {
            $request->setDomain(\Laminas\Uri\UriFactory::factory($baseUrl)->getHost());
        } elseif (class_exists('\Zend\Uri\Http')) {
            $request->setDomain(\Zend\Uri\UriFactory::factory($baseUrl)->getHost());
        }
        $response = $this->postNewRecording->execute($this->config->getServiceId(), $request);

        $recording = $this->recordingFactory->create();

        $initiator = $this->appState->getAreaCode() === Area::AREA_CRONTAB
            ? RecordingInterface::INITIATOR_CRON
            : RecordingInterface::INITIATOR_USER;

        $scheduledAtTs = $scheduledAtTs ?? time();

        if ($duration === null) {
            if ($runMode === RunMode::MODE_AUTO) {
                $autoRunType = $this->config->getAutoRunType($storeId);
                if ($autoRunType === AutoRunType::SCHEDULED) {
                    $recordSchedule = $this->config->getRecordSchedule($storeId);
                    if (empty($recordSchedule)) {
                        throw new LocalizedException(__('No auto run schedule configured for store %1', $storeId));
                    }
                    $schedule = $this->autoRunScheduleFactory->create([
                        'schedule' => $recordSchedule
                    ]);
                    $duration = $schedule->getRecordForDuration(1);
                } elseif ($autoRunType === AutoRunType::CONTINUOUS) {
                    $recordSchedule = [];
                    $duration = $this->config->getAutoRunDuration($storeId);
                } else {
                    throw new LocalizedException(__('Unknown auto run type %1', $autoRunType));
                }
                $recording->setRecordSchedule($recordSchedule); // copy from config to individual recording
                $recording->setDuration($duration);
                $recording->setAutoRunType($autoRunType);
            } else {
                $recording->setDuration(RecordingInterface::DEFAULT_DURATION);
            }
        } else {
            $recording->setDuration($duration);
        }

        $recording->setStoreId($storeId);
        $recording->setScheduledAt($this->dateTimeFmt->formatDate($scheduledAtTs));
        $recording->setCreatedAt($this->dateTimeFmt->formatDate(true));
        $recording->setInitiator($initiator);
        $recording->setUuid($response->getId());
        $recording->setPageHandlePriority($response->getDefaultPageHandlePriority());
        $recording->setStatus(RecordingInterface::STATUS_PENDING);
        $recording->setStaticVersion($this->deployedVersion->getValue());

        return $recording;
    }
}
