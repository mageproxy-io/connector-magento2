<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Controller\Adminhtml\Recording;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ApiClient\GetRecordingJsDepsCountTsInterface;

class DepsChart extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_view';

    private GetRecordingJsDepsCountTsInterface $getRecordingJsDepsCountTs;
    private RecordingRepositoryInterface $recordingRepository;
    private OptimizationRepositoryInterface $optimizationRepository;

    public function __construct(
        Context $context,
        GetRecordingJsDepsCountTsInterface $getRecordingJsDepsCountTs,
        RecordingRepositoryInterface $recordingRepository,
        OptimizationRepositoryInterface $optimizationRepository
    ) {
        parent::__construct($context);
        $this->getRecordingJsDepsCountTs = $getRecordingJsDepsCountTs;
        $this->recordingRepository = $recordingRepository;
        $this->optimizationRepository = $optimizationRepository;
    }

    public function execute()
    {
        $json = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $recordingId = (int) $this->getRequest()->getParam('recording_id');
        if (empty($recordingId)) {
            return $json;
        }
        try {
            $recording = $this->recordingRepository->getById($recordingId);
            $result = $this->getRecordingJsDepsCountTs->execute($recording->getUuid());

            // convert this data into format our chart can understand
            $depsDataPoints = array_map(function ($dataPoint) {
                return [
                    'x' => $this->format($dataPoint['timestamp']),
                    'y' => $dataPoint['value'],
                ];
            }, $result);

            $optimizations = $this->optimizationRepository->getByRecordingId($recordingId);
            $deployedOptimizations = array_filter(
                $optimizations->getItems(),
                fn ($optimization) => $optimization->getDeployedAt() !== null
            );

            $optimizationsDataPoints = array_map(function ($optimization) {
                return [
                    'x' => $this->format($optimization->getDeployedAt()),
                    'y' => $optimization->getDepsCount(),
                ];
            }, array_values($deployedOptimizations));


            $json->setData([
                'depsTs' => $depsDataPoints,
                'optsTs' => $optimizationsDataPoints
            ]);
        } catch (\Exception $e) {
        }
        return $json;
    }

    private function format($dateTime)
    {
        if ($dateTime instanceof \DateTime) {
            $timestamp = $dateTime->getTimestamp();
        } elseif (is_string($dateTime)) {
            $timestamp = (new \DateTime($dateTime))->getTimestamp();
        } elseif (is_int($dateTime)) {
            $timestamp = $dateTime;
        } else {
            throw new \InvalidArgumentException('Invalid date time format');
        }
        return $timestamp * 1000;
    }
}
