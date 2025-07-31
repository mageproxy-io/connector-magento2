<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ResourceModel\Optimization;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;

class Template extends AbstractDb
{
    public const MAIN_TABLE_NAME = 'mageproxy_optimization_template';
    public const MAIN_TABLE_ID_FIELD = 'template_id';

    protected $_serializableFields = [
        OptimizationTemplateInterface::EXCLUDE_DEPS => [[], []],
        OptimizationTemplateInterface::REMOVE_DEPS => [[], []],
        OptimizationTemplateInterface::HANDLES => [[], []],
        OptimizationTemplateInterface::TRANSPILE_GLOBS => [[], []],
    ];

    public function __construct(
        Context $context,
        Json $serializer,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->serializer = $serializer;
    }

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD);
    }

    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [
            ['field' => ['store_id'], 'title' => __('Optimization template for Specified Store')],
        ];
        return $this;
    }

}
