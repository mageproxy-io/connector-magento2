<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\DataProvider\Form;

use Magento\Framework\Escaper;
use Magento\Store\Model\System\Store;

class StoreFieldRenderer
{
    private Store $systemStore;
    private Escaper $escaper;

    public function __construct(
        Store $systemStore,
        Escaper $escaper
    ) {
        $this->systemStore = $systemStore;
        $this->escaper = $escaper;
    }

    public function getText(int $storeId): string
    {
        if ($storeId === 0) {
            return (string)__('All Store Views');
        }
        $structure = $this->systemStore->getStoresStructure(false, [$storeId]);
        $content = '';
        foreach ($structure as $website) {
            $content .= $website['label'] . '<br/>';
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . $this->escaper->escapeHtml($group['label']) . '<br/>';
                foreach ($group['children'] as $store) {
                    if ($store['value'] == $storeId) {
                        $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . '<br/>';
                        break;
                    }
                }
            }
        }
        return $content;

    }
}
