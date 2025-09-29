<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Controller\Adminhtml\Prefetch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Mageproxy\Connector\Model\PrefetchFactory;
use Mageproxy\Connector\Model\ResourceModel\Prefetch as PrefetchResource;

class Delete extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Mageproxy_Connector::prefetch_delete';

    private PrefetchFactory $prefetchFactory;
    private PrefetchResource $prefetchResource;

    public function __construct(Context $context, PrefetchFactory $prefetchFactory, PrefetchResource $prefetchResource)
    {
        parent::__construct($context);
        $this->prefetchFactory = $prefetchFactory;
        $this->prefetchResource = $prefetchResource;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        try {
            $model = $this->prefetchFactory->create();
            $this->prefetchResource->load($model, $id);
            if ($model->getId()) {
                $this->prefetchResource->delete($model);
                $this->messageManager->addSuccessMessage(__('Prefetch definition deleted.'));
            }
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(__('Delete failed: %1', $e->getMessage()));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('mageproxy/prefetch/index');
    }
}
