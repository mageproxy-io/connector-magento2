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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageproxy\Connector\Api\Data\PrefetchInterface;
use Mageproxy\Connector\Model\PrefetchFactory;
use Mageproxy\Connector\Model\ResourceModel\Prefetch as PrefetchResource;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Mageproxy_Connector::prefetch_edit';

    private PrefetchFactory $prefetchFactory;
    private PrefetchResource $prefetchResource;

    public function __construct(
        Context $context,
        PrefetchFactory $prefetchFactory,
        PrefetchResource $prefetchResource
    ) {
        parent::__construct($context);
        $this->prefetchFactory = $prefetchFactory;
        $this->prefetchResource = $prefetchResource;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('prefetch_id');
        $storeId = (int)$this->getRequest()->getParam('store_id', 0);
        $rules = $this->getRequest()->getParam('rules', []);

        // Normalize posted rules keys to selector,bundle_pattern,prefetch_on
        $rules = $this->normalizeRules($rules);

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            if (!$id) {
                // New definition: must not exist for the store
                $existing = $this->prefetchFactory->create();
                $this->prefetchResource->load($existing, $storeId, 'store_id');
                if ($existing->getId()) {
                    throw new LocalizedException(
                        __('A prefetch rules entry already exists for the selected store view.')
                    );
                }
                $model = $this->prefetchFactory->create();
                $model->setData('store_id', $storeId);
                $model->setData('rules', $rules);
                $this->prefetchResource->save($model);
                $this->messageManager->addSuccessMessage(__('Prefetch rules created.'));
                return $resultRedirect->setPath('mageproxy/prefetch/index');
            }

            // Edit existing
            $model = $this->prefetchFactory->create();
            $this->prefetchResource->load($model, $id);
            if (!$model->getId()) {
                throw new LocalizedException(__('Prefetch rules not found.'));
            }

            // If store_id is changing to one that exists on another record, block
            $existing = $this->prefetchFactory->create();
            $this->prefetchResource->load($existing, $storeId, 'store_id');
            if ($existing->getId() && (int)$existing->getId() !== $id) {
                throw new LocalizedException(
                    __('Another prefetch rules entry already exists for the selected store view.')
                );
            }

            $model->setData('store_id', $storeId);
            $model->setData('rules', $rules);
            $this->prefetchResource->save($model);
            $this->messageManager->addSuccessMessage(__('Prefetch rules saved.'));
            return $resultRedirect->setPath('mageproxy/prefetch/index');
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $path = $id ? 'mageproxy/prefetch/edit' : 'mageproxy/prefetch/new';
            $params = $id ? ['id' => $id] : [];
            return $resultRedirect->setPath($path, $params);
        }
    }

    private function normalizeRules(array $rules): array
    {
        $out = [];
        foreach ($rules as $rule) {
            $selector = trim((string)($rule['selector'] ?? ''));
            $bundle = trim((string)($rule['bundle_pattern'] ?? ($rule['bundle'] ?? '')));
            $prefetchOn = trim((string)($rule['prefetch_on'] ?? ($rule['trigger'] ?? '')));
            if ($selector === '' || $bundle === '') {
                continue;
            }
            if ($prefetchOn !== PrefetchInterface::TRIGGER_VIEWPORT && $prefetchOn !== PrefetchInterface::TRIGGER_INTERACTION) {
                $prefetchOn = PrefetchInterface::TRIGGER_INTERACTION;
            }
            $out[] = [
                PrefetchInterface::RULE_SELECTOR => $selector,
                PrefetchInterface::RULE_BUNDLE_PATTERN => $bundle,
                PrefetchInterface::RULE_PREFETCH_ON => $prefetchOn,
            ];
        }
        return $out;
    }

    
}
