<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Provider;

use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\ProviderInterface;

class OptimizationsProvider implements ProviderInterface
{
    private OptimizationRepositoryInterface $optimizationRepository;
    private SearchCriteriaProvider $searchCriteriaProvider;

    public function __construct(
        SearchCriteriaProvider $searchCriteriaProvider,
        OptimizationRepositoryInterface $optimizationRepository
    ) {
        $this->optimizationRepository = $optimizationRepository;
        $this->searchCriteriaProvider = $searchCriteriaProvider;
    }

    public function getItems(): array
    {
        $result = $this->optimizationRepository->getList(
            $this->searchCriteriaProvider->getSearchCriteria()
        );
        return $result->getItems();
    }
}
