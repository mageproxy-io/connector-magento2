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

use Magento\Framework\Api\ExtensibleDataInterface;

interface DistributionInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getEdition(): string;

    /**
     * @param string $edition
     * @return void
     */
    public function setEdition(string $edition): void;

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @param string $version
     * @return void
     */
    public function setVersion(string $version): void;

    /**
     * @return string|null
     */
    public function getRevision(): ?string;

    /**
     * @param string|null $revision
     * @return void
     */
    public function setRevision(?string $revision): void;
}
