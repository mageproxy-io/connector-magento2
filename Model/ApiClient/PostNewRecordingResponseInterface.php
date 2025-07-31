<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ApiClient;

interface PostNewRecordingResponseInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $id
     * @return void
     */
    public function setId(string $id): void;

    /**
     * @return string[]
     */
    public function getDefaultPageHandlePriority(): array;

    /**
     * @param string[] $defaultPageHandlePriority
     * @return void
     */
    public function setDefaultPageHandlePriority(array $defaultPageHandlePriority): void;
}
