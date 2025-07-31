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

interface ProcessorInterface
{
    /**
     * @param $entity
     * @return void
     */
    public function process($entity): void;

    /**
     * @return \Mageproxy\Connector\Model\ProviderInterface
     */
    public function getProvider(): ProviderInterface;

    /**
     * @return string[]
     */
    public function getMessages(): array;

    /**
     * @return bool
     */
    public function canRun(): bool;

}
