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

class PostNewRecordingResponse implements PostNewRecordingResponseInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string[]
     */
    private array $defaultPageHandlePriority = [];

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string[]
     */
    public function getDefaultPageHandlePriority(): array
    {
        return $this->defaultPageHandlePriority;
    }

    /**
     * @param string[] $defaultPageHandlePriority
     * @return void
     */
    public function setDefaultPageHandlePriority(array $defaultPageHandlePriority): void
    {
        $this->defaultPageHandlePriority = $defaultPageHandlePriority;
    }
}
