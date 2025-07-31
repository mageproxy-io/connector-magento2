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
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;

class SaveAsTemplateButton implements ButtonProviderInterface
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

        if (!in_array($optimization->getStatus(), [
            OptimizationInterface::STATUS_DEPLOYED,
            OptimizationInterface::STATUS_READY
        ])) {
            return [];
        }

        if ($optimization->getTemplateId()) {
            return [];
        }

        $targetName = 'mageproxy_optimization_view.mageproxy_optimization_view.save_as_template_modal';

        return [
            'label' => __('Save as Auto Run Template'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => $targetName,
                                'actionName' => 'openModal',
                                'params' => [
                                    [
                                        'url' => $this->getUrl($optimizationId)
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],
            'on_click' => '',
            'sort_order' => 20
        ];
    }

    private function getUrl($optimizationId): string
    {
        return $this->urlFactory->create()->getUrl('mageproxy/optimization/saveAsTemplate', [
            'optimization_id' => $optimizationId
        ]);
    }
}
