<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Duration implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        for ($i = 5; $i <= 30; $i += 5) {
            $options[] = [
                'label' => __($i . ' minutes'),
                'value' => $i,
            ];
        }
        return $options;
    }
}
