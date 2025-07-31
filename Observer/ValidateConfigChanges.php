<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Observer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\ConfigValidationFailedFlag;
use Mageproxy\Connector\Model\ConfigValidator;

/**
 * Listen to admin system config changes to the module's config section
 * and validate the changes.
 */
class ValidateConfigChanges implements ObserverInterface
{
    private ConfigValidator $configValidator;
    private ManagerInterface $messageManager;
    private ScopeConfigInterface $scopeConfig;
    private ConfigValidationFailedFlag $validationFailedFlag;
    private OptimizationRepositoryInterface $optimizationRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        ConfigValidator $configValidator,
        ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        ConfigValidationFailedFlag $validationFailedFlag,
        OptimizationRepositoryInterface $optimizationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->configValidator = $configValidator;
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->validationFailedFlag = $validationFailedFlag;
        $this->optimizationRepository = $optimizationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function execute(Observer $observer)
    {
        $changedPaths = $observer->getEvent()->getChangedPaths();
        if (empty($changedPaths)) {
            return;
        }

        if (in_array(Config::XML_PATH_IS_ENABLED, $changedPaths)) {
            $disabled = !$enabled = (bool) $this->scopeConfig->getValue(Config::XML_PATH_IS_ENABLED);
            if ($enabled) {
                // Module was enabled
                $result = $this->configValidator->validate();
                if (!empty($result['errors'])) {
                    foreach ($result['errors'] as $errorMessage) {
                        $this->messageManager->addNoticeMessage($errorMessage);
                    }
                    $this->validationFailedFlag->set();
                    return;
                }
            }
            if ($disabled) {
                $this->messageManager->addNoticeMessage(
                    __('Any running recordings or deployed optimizations will be reset on the next cron run.')
                );
            }
            $this->validationFailedFlag->clear();
        }
    }
}
