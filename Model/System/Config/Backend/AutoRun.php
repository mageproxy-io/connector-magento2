<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\System\Config\Backend;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\System\Config\Source\RunMode;

class AutoRun extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Mageproxy\Connector\Api\OptimizationRepositoryInterface
     */
    private OptimizationRepositoryInterface $optimizationRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        OptimizationRepositoryInterface $optimizationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->optimizationRepository = $optimizationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function beforeSave()
    {
        if ($this->isValueChanged()) {
            $mode = $this->getValue();
            if ($mode === RunMode::MODE_AUTO) {
                $hasDeployments = $this->optimizationRepository->getList(
                        $this->searchCriteriaBuilder->addFilter(
                            OptimizationInterface::STATUS,
                            OptimizationInterface::STATUS_DEPLOYED
                        )->create()
                    )->getTotalCount() > 0;
                if ($hasDeployments) {
                    throw new LocalizedException(
                        __('You cannot change to auto run mode when there are deployed optimizations. '
                            . 'First revert any deployed optimizations.')
                    );
                }

            }
        }
        return parent::beforeSave();
    }
}
