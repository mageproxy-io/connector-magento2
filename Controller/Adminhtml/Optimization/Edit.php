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

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = "Mageproxy_Connector::proxy";

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $page->getConfig()->getTitle()->prepend(__('Generate JS Bundles'));
        $page->setActiveMenu('Mageproxy_Connector::mageproxy');
        return $page;
    }
}
