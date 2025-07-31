<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ResourceModel;

use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mageproxy\Connector\Api\Data\OptimizationInterface;

class Optimization extends AbstractDb
{
    public const MAIN_TABLE_NAME = 'mageproxy_optimization';
    public const MAIN_TABLE_PRIMARY_KEY = 'optimization_id';

    protected $_serializableFields = [
        OptimizationInterface::HANDLES => [[], []],
        OptimizationInterface::EXCLUDE_DEPS => [[], []],
        OptimizationInterface::REMOVE_DEPS => [[], []],
        OptimizationInterface::TRANSPILE_GLOBS => [[], []]
    ];

    private IdentityGeneratorInterface $identityGenerator;

    public function __construct(
        Context $context,
        IdentityGeneratorInterface $identityGenerator,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->identityGenerator = $identityGenerator;
    }

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_PRIMARY_KEY);
    }

    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->getData(OptimizationInterface::UUID) === null) {
            $object->setData(
                OptimizationInterface::UUID,
                $this->identityGenerator->generateId()
            );
        }
        if ($object->getData(OptimizationInterface::STATUS) === null) {
            $object->setData(
                OptimizationInterface::STATUS,
                OptimizationInterface::STATUS_REQUESTED
            );
        }

        if (!$object->getData(OptimizationInterface::STORE_ID)) {
            if ($object->getRecording() === null) {
                throw new CouldNotSaveException(__('Recording is required'));
            }
            $object->setData(
                OptimizationInterface::STORE_ID,
                $object->getRecording()->getStoreId()
            );
        }

        if (!$object->getData(OptimizationInterface::RECORDING_ID)) {
            if ($object->getRecording() === null) {
                throw new CouldNotSaveException(__('Recording is required'));
            }
            $object->setData(
                OptimizationInterface::RECORDING_ID,
                $object->getRecording()->getId()
            );
        }

        return $this;
    }
}
