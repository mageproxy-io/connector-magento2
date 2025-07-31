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

use Mageproxy\Connector\Model\ProviderInterface;

/**
 * Base class to implement a processor used in a cron job
 * that will receive a provider and condition to run
 */
abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * @var \Mageproxy\Connector\Model\ProviderInterface
     */
    private ProviderInterface $provider;

    /**
     * @var array
     */
    protected array $messages = [];

    public function __construct(
        ProviderInterface $provider
    ) {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public abstract function process($entity): void;

    /**
     * @inheritdoc
     */
    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }

    /**
     * @inheritdoc
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @inheritdoc
     */
    public function canRun(): bool
    {
        return true;
    }
}
