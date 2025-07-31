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

class GetRecordingSnapshot implements GetRecordingSnapshotInterface
{
    /**
     * @var \Mageproxy\Connector\Model\ApiClient\Adapter
     */
    private Adapter $adapter;

    /**
     * @param \Mageproxy\Connector\Model\ApiClient\Adapter $adapter
     */
    public function __construct(
        Adapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $uuid): ?getRecordingSnapshotResponseInterface
    {
        return $this->adapter->get(['id' => $uuid]);
    }
}
