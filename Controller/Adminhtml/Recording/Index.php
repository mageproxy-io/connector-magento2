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
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\ConfigValidationFailedFlag;
use Mageproxy\Connector\Model\ConfigValidator;

class Index extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_view';

    private PageFactory $pageFactory;
    private Config $config;
    private ConfigValidator $configValidator;
    private ConfigValidationFailedFlag $configValidationFailedFlag;

    public function __construct(
        PageFactory $pageFactory,
        Context $context,
        Config $config,
        ConfigValidator $configValidator,
        ConfigValidationFailedFlag $configValidationFailedFlag
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->config = $config;
        $this->configValidator = $configValidator;
        $this->configValidationFailedFlag = $configValidationFailedFlag;
    }

    public function execute()
    {
        if (!$this->config->getIsEnabled()) {
            $msg = __(
                'Mageproxy Connector is disabled. '
                . 'Please enable it in Stores > Configuration > Services > Mageproxy Connector.'
            );
            $this->messageManager->addWarningMessage($msg);
        }

        $result = $this->configValidator->validate();
        if (!empty($result['errors'])) {
            $this->configValidationFailedFlag->set();
            foreach ($result['errors'] as $errorMessage) {
                $this->messageManager->addWarningMessage($errorMessage);
            }
        } else {
            $this->configValidationFailedFlag->clear();
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Mageproxy_Connector::mageproxy');
        $resultPage->getConfig()->getTitle()->prepend(__('Recordings'));

        return $resultPage;
    }
}
