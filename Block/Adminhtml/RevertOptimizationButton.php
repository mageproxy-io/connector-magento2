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


use Magento\Backend\Model\UrlFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

class RevertOptimizationButton implements ButtonProviderInterface
{
    private UrlFactory $urlFactory;
    private RequestInterface $request;
    private OptimizationRepositoryInterface $optimizationRepository;
    private RecordingRepositoryInterface $recordingRepository;
    private OptimizationManagerInterface $optimizationManager;

    public function __construct(
        UrlFactory $urlFactory,
        RequestInterface $request,
        OptimizationRepositoryInterface $optimizationRepository,
        RecordingRepositoryInterface $recordingRepository,
        OptimizationManagerInterface $optimizationManager
    ) {
        $this->urlFactory = $urlFactory;
        $this->request = $request;
        $this->optimizationRepository = $optimizationRepository;
        $this->recordingRepository = $recordingRepository;
        $this->optimizationManager = $optimizationManager;
    }

    public function getButtonData()
    {
        $optimizationId = (int) $this->request->getParam('optimization_id');
        $buttonClass = 'primary';

        if (!$optimizationId) {
            // See if we can get it from the recording ID
            $recordingId = (int) $this->request->getParam('id');
            $recording = $this->recordingRepository->getById($recordingId);
            $optimization = $this->optimizationManager->getDeployedOptimization((int) $recording->getStoreId());
            if ($optimization && $optimization->getRecordingId() === $recordingId) {
                $optimizationId = (int) $optimization->getId();
                $buttonClass = 'secondary';
            } else {
                return [];
            }
        }

        $optimization = $this->optimizationRepository->getById($optimizationId);

        if ($optimization->getStatus() !== OptimizationInterface::STATUS_DEPLOYED) {
            return [];
        }

        return [
            'label' => __('Revert Deployment'),
            'on_click' => 'deleteConfirm(\'' . __(
                'Are you sure you want to do this?'
            ) . '\', \'' . $this->getUrl($optimizationId) . '\')',
            'class' => $buttonClass,
            'sort_order' => 90
        ];
    }

    private function getUrl($optimizationId): string
    {
        return $this->urlFactory->create()->getUrl('mageproxy/optimization/revert', [
            'optimization_id' => $optimizationId
        ]);
    }
}
