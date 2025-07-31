<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Controller\Adminhtml\Optimization;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Create extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::optimization_create';

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $page->getConfig()->getTitle()->prepend(__('Create Optimization'));
        $page->setActiveMenu('Mageproxy_Connector::mageproxy');
        return $page;
    }
}
