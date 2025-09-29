<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Api\Data;

/**
 * @api
 */
interface PrefetchInterface
{
    // Data keys
    public const STORE_ID = 'store_id';
    public const RULES = 'rules';

    // Rule keys
    public const RULE_SELECTOR = 'selector';
    public const RULE_BUNDLE_PATTERN = 'bundle_pattern';
    public const RULE_PREFETCH_ON = 'prefetch_on';

    // Trigger values
    public const TRIGGER_INTERACTION = 'interaction';
    public const TRIGGER_VIEWPORT = 'viewport';

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     * @return void
     */
    public function setStoreId(int $storeId): void;

    /**
     * @return array
     */
    public function getRules(): array;

    /**
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules): void;
}
