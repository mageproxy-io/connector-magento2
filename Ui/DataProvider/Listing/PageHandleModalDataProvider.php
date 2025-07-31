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

class PageHandleModalDataProvider extends DependencyDataProvider
{
    protected function _initSelect()
    {
        parent::_initSelect();

        // Reset anything previously set by the parent
        $this->getSelect()->reset(Select::ORDER);
        $this->getSelect()->reset(Select::COLUMNS);
        $this->getSelect()->reset(Select::GROUP);

        $this->getSelect()->columns(
            [
                'page_handle',
                'min_priority' => new \Zend_Db_Expr('MIN(priority)')
            ]
        );

        // We want unique page handles
        $this->getSelect()->group('page_handle');

        // Sort from highest to lowest priority
        $this->getSelect()->order('min_priority ASC');

        return $this;
    }
}
