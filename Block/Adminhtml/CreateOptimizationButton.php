<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Block\Adminhtml;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

class CreateOptimizationButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var \Mageproxy\Connector\Api\RecordingManagerInterface
     */
    private RecordingManagerInterface $recordingManager;
    /**
     * @var \Mageproxy\Connector\Api\RecordingRepositoryInterface
     */
    private RecordingRepositoryInterface $recordingRepository;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mageproxy\Connector\Api\RecordingManagerInterface $recordingManager
     * @param \Mageproxy\Connector\Api\RecordingRepositoryInterface $recordingRepository
     */
    public function __construct(
        RequestInterface $request,
        RecordingManagerInterface $recordingManager,
        RecordingRepositoryInterface $recordingRepository
    ) {
        $this->request = $request;
        $this->recordingManager = $recordingManager;
        $this->recordingRepository = $recordingRepository;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $recordingId = (int) $this->request->getParam('id');

        if (!$this->recordingManager->hasDependencies($recordingId)) {
            return [];
        }


        try {
            $recording = $this->recordingRepository->getById($recordingId);
            if ($recording->getStatus() === RecordingInterface::STATUS_FINISHED ||
                $this->recordingManager->shouldFinish($recording)
            ) {
                return [];
            }
        } catch (NoSuchEntityException $e) {
            return [];
        }

        // We are going to allow optimizations even when in auto optimize mode

        return [
            'label' => __('Create Optimization'),
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->getFormTarget(),
                                'actionName' => 'destroyInserted'
                            ],
                            [
                                'targetName' => $this->getModalTarget(),
                                'actionName' => 'openModal'
                            ],
                            [
                                'targetName' => $this->getFormTarget(),
                                'actionName' => 'render'
                            ],
                        ],
                    ],
                ]
            ],
            'on_click' => '',
            'class' => 'action-secondary',
            'sort_order' => 99
        ];
    }

    /**
     * @return string
     */
    private function getFormTarget(): string
    {
        return $this->getModalTarget() . '.create_optimization_insert_form';
    }

    /**
     * @return string
     */
    private function getModalTarget(): string
    {
        return 'mageproxy_recording_form'
            . '.mageproxy_recording_form'
            . '.create_optimization_form_modal'
            ;
    }
}
