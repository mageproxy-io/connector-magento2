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
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;

class DeployOptimizationButton implements ButtonProviderInterface
{
    private UrlFactory $urlFactory;
    private RequestInterface $request;
    private OptimizationRepositoryInterface $optimizationRepository;

    public function __construct(
        UrlFactory $urlFactory,
        RequestInterface $request,
        OptimizationRepositoryInterface $optimizationRepository
    ) {
        $this->urlFactory = $urlFactory;
        $this->request = $request;
        $this->optimizationRepository = $optimizationRepository;
    }

    public function getButtonData()
    {
        $optimizationId = (int) $this->request->getParam('optimization_id');

        $optimization = $this->optimizationRepository->getById($optimizationId);

        if ($optimization->getStatus() !== OptimizationInterface::STATUS_READY) {
            return [];
        }

        if ($optimization->getRecording()->getStatus() === RecordingInterface::STATUS_FINISHED) {
            return [];
        }

        return [
            'label' => __('Deploy'),
            'on_click' => 'deleteConfirm(\'' . __(
                'Are you sure you want to do this?'
            ) . '\', \'' . $this->getUrl($optimizationId) . '\')',
            'class' => 'primary'
        ];
    }

    private function getUrl($optimizationId): string
    {
        return $this->urlFactory->create()->getUrl('mageproxy/optimization/deploy', [
            'optimization_id' => $optimizationId
        ]);
    }
}
