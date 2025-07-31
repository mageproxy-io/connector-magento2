<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\DataProvider\Listing;

use Magento\Framework\DB\Select;

class DependencyModalDataProvider extends DependencyDataProvider
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->reset(Select::ORDER);
        $this->getSelect()->group('module_id');
        return $this;
    }

}
