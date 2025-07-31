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

/**
 * DTO
 */
class Distribution implements DistributionInterface
{
    private string $edition;
    private string $version;
    private ?string $revision = null; // optional

    /**
     * @inheritdoc
     */
    public function getEdition(): string
    {
        return $this->edition;
    }

    /**
     * @inheritdoc
     */
    public function setEdition(string $edition): void
    {
        $this->edition = $edition;
    }

    /**
     * @inheritdoc
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @inheritdoc
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @inheritdoc
     */
    public function getRevision(): ?string
    {
        return $this->revision;
    }

    /**
     * @inheritdoc
     */
    public function setRevision(?string $revision): void
    {
        $this->revision = $revision;
    }
}
