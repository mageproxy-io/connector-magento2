<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Observer\Adminhtml;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Mageproxy\Connector\Model\ConfigValidationFailedFlag;

class ControllerPreDispatch implements ObserverInterface
{
    private ConfigValidationFailedFlag $flag;
    private ManagerInterface $messageManager;
    private ActionFlag $actionFlag;
    private RedirectInterface $redirect;
    private array $checkControllersForValidationFlag;

    public function __construct(
        ConfigValidationFailedFlag $flag,
        ManagerInterface $messageManager,
        ActionFlag $actionFlag,
        RedirectInterface $redirect,
        array $checkControllersForConfigValidationFlag = []
    ) {
        $this->flag = $flag;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->redirect = $redirect;
        $this->checkControllersForValidationFlag = $checkControllersForConfigValidationFlag;
    }

    public function execute(Observer $observer)
    {
        /** @var  \Magento\Framework\App\Request\Http $request */
        $request = $observer->getRequest();

        if (!in_array($request->getFullActionName(), array_values($this->checkControllersForValidationFlag))) {
            return;
        }

        if ($this->flag->has()) {
            /** @var \Magento\Backend\App\Action $controller */
            $controller = $observer->getControllerAction();

            $message = __('Mageproxy Connector configuration validation failed. Check system messages for guidance.');

            $this->messageManager->addNoticeMessage($message);
            $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
            $this->redirect->redirect($controller->getResponse(), '*/*');
        }

    }
}
