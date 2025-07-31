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

use Mageproxy\Connector\Model\PurgeFullPageCache;
use Psr\Log\LoggerInterface;

/**
 * Represents a cron job consisting of one or more processors.
 * Aggregates processors and executes them.
 * Logs and handles errors coming from processors.
 */
class Job
{
    /**
     * @var \Mageproxy\Connector\Cron\ProcessorInterface[]
     */
    private array $processors;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var \Mageproxy\Connector\Model\PurgeFullPageCache
     */
    private PurgeFullPageCache $purgeFullPageCache;

    /**
     * @var \Mageproxy\Connector\Cron\RunConditionInterface
     */
    private RunConditionInterface $condition;

    public function __construct(
        LoggerInterface $logger,
        PurgeFullPageCache $purgeFullPageCache,
        RunConditionInterface $condition,
        array $processors = []
    ) {
        $this->processors = $processors;
        $this->logger = $logger;
        $this->purgeFullPageCache = $purgeFullPageCache;
        $this->condition = $condition;
    }

    public function execute(): void
    {
        if (!$this->condition->canRun()) {
            return;
        }
        foreach ($this->getProcessors() as $processor) {
            if (!$processor instanceof ProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Processor must implement %s',
                        ProcessorInterface::class
                    )
                );
            }
            if (!$processor->canRun()) {
                continue;
            }
            foreach ($processor->getProvider()->getItems() as $item) {
                try {
                    $processor->process($item);
                    foreach ($processor->getMessages() as $message) {
                        $this->logger->info($message);
                    }
                } catch (\Throwable $e) {
                    $this->logger->critical($e);
                }
            }
        }
        $this->purgeFullPageCache->execute();
    }

    public function getProcessors(): array
    {
        return $this->processors;
    }
}
