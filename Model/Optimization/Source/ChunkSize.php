<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Optimization\Source;

class ChunkSize implements ChunkSizeSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        for ($i = 300; $i <= 1000; $i += 100) {
            $options[] = ['value' => $i, 'label' => (string) $i];
        }
        return $options;
    }
}
