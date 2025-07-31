<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Controller\Adminhtml\Recording;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\System\Config\Source\RunMode;

class Create extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_create';

    private PageFactory $pageFactory;
    private Config $config;
    private StoreManagerInterface $storeManager;

    public function __construct(
        PageFactory $pageFactory,
        RedirectFactory $resultRedirectFactory,
        Config $config,
        StoreManagerInterface $storeManager,
        Context $context
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->config = $config;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        if (!$this->config->getIsEnabled()) {
            $this->messageManager->addErrorMessage(__('The mageproxy.io connector is disabled.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        $stores = $this->storeManager->getStores();
        $stores = array_filter($stores, function ($store) {
            return $this->config->getRunMode($store->getId()) === RunMode::MODE_MANUAL;
        });

        if (empty($stores)) {
            $this->messageManager->addErrorMessage(__('All stores are setup to run in auto run mode.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        $mode = $this->getRequest()->getParam('mode');

        if ($mode === RecordingInterface::MODE_IMMEDIATE) {
            if ($this->storeManager->isSingleStoreMode()) {
                $this->_forward('save', null, null, ['mode' => RecordingInterface::MODE_IMMEDIATE]);
            }
        }

        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Mageproxy_Connector::mageproxy');
        $resultPage->getConfig()->getTitle()->prepend(__('Create Recording'));
        return $resultPage;
    }
}
