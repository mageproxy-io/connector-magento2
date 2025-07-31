<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Cron\Processor\AutoRun;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Cron\AbstractProcessor;
use Mageproxy\Connector\Model\ProviderInterface;
use Mageproxy\Connector\Model\System\Config\Source\AutoRunType;

class DeployAndRevert extends AbstractProcessor
{
    private OptimizationRepositoryInterface $optimizationRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private SortOrderBuilder $sortOrderBuilder;
    private OptimizationManagerInterface $optimizationManager;

    public function __construct(
        ProviderInterface $provider,
        OptimizationRepositoryInterface $optimizationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        OptimizationManagerInterface $optimizationManager
    ) {
        parent::__construct($provider);
        $this->optimizationRepository = $optimizationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->optimizationManager = $optimizationManager;
    }

    public function process($entity): void
    {
        /** @var \Mageproxy\Connector\Api\Data\RecordingInterface $recording */
        $recording = $entity;

        // Anything done directly by the user should not be touched in auto run mode
        if ($recording->getInitiator() === RecordingInterface::INITIATOR_USER) {
            return;
        }

        if (!in_array($recording->getAutoRunType(), [
            AutoRunType::SCHEDULED,
            AutoRunType::CONTINUOUS
        ])) {
            return;
        }

        if (!in_array($recording->getStatus(),
            [
                RecordingInterface::STATUS_RUNNING,
                RecordingInterface::STATUS_STOPPED
            ]
        )) {
            return;
        }

        $optimizations = $this->getOptimizations($recording);

        /** @var OptimizationInterface $last */
        $last = array_shift($optimizations);
        if (!$last ||
            $last->getStatus() === OptimizationInterface::STATUS_DEPLOYED ||
            $last->getRequestedBy() !== OptimizationInterface::REQUESTED_BY_CRON // If requested by  user, should be deployed by user
        ) {
            // If the last requested optimization is deployed, we don't need to do anything
            // there wasn't a newer requested optimization that read to go
            return;
        }

        // This has to be a "deployable" optimization, but before we deploy it, let's revert the one
        // just before that, or just to be safe, all of them
        foreach ($optimizations as $optimization) {
            $this->optimizationManager->revert($optimization, OptimizationInterface::STATUS_FINISHED);
        }

        // Now, the magic happens...
        $this->optimizationManager->deploy($last);
    }

    private function getOptimizations($recording): array
    {
        $sortBy = $this->sortOrderBuilder
            ->setField(OptimizationInterface::REQUESTED_AT)
            ->setDescendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                'recording_id', $recording->getId()
            )->addFilter(
                'status',
                [
                    OptimizationInterface::STATUS_DEPLOYED,
                    OptimizationInterface::STATUS_READY
                ],
                'in'
            )->addSortOrder(
                $sortBy
            )->create();

        return $this->optimizationRepository->getList($searchCriteria)->getItems();
    }
}
