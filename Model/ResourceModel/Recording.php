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

use DateTime;
use Magento\Framework\App\View\Deployment\Version as DeployVersion;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;

class Recording extends AbstractDb
{
    public const TABLE_NAME = 'mageproxy_recording';
    public const TABLE_PRIMARY_KEY = 'recording_id';

    private IdentityGeneratorInterface $identityGenerator;
    private DeployVersion $deployedVersion;
    private StoreManagerInterface $storeManager;

    protected $_serializableFields = [
        RecordingInterface::RECORD_SCHEDULE => [[], []],
        RecordingInterface::PAGE_HANDLE_PRIORITY => [[], []]
    ];

    public function __construct(
        Context $context,
        IdentityGeneratorInterface $identityGenerator,
        DeployVersion $deployedVersion,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->identityGenerator = $identityGenerator;
        $this->deployedVersion = $deployedVersion;
        $this->storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);
    }

    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [
            [
                'field' => ['uuid'],
                'title' => __('UUID'),
            ],
        ];
        return $this;
    }

    protected function _beforeSave(AbstractModel $object)
    {
        // Setting mandatory fields to defaults when they omitted

        if ($object->getData(RecordingInterface::UUID) === null) {
            $object->setData(
                RecordingInterface::UUID,
                $this->identityGenerator->generateId()
            );
        }

        if ($object->getData(RecordingInterface::SCHEDULED_AT) === null) {
            $object->setData(
                RecordingInterface::SCHEDULED_AT,
                (new DateTime())->format('Y-m-d H:i:s')
            );
        }

        if ($object->getData(RecordingInterface::STORE_ID) === null) {
            $object->setData(
                RecordingInterface::STORE_ID,
                $this->storeManager->getStore()->getId()
            );
        }

        if ($object->getData(RecordingInterface::STATUS) === null) {
            $object->setData(
                RecordingInterface::STATUS,
                RecordingInterface::STATUS_PENDING
            );
        }

        if ($this->getRecordingOverlaps($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Recording interval overlaps with another recording')
            );
        }

        return parent::_beforeSave($object);
    }

    public function getRecordingOverlaps(AbstractModel $object): bool
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('store_id = ?', $object->getStoreId())
            ->where('recording_id != ?', $object->getId())
            ->where('status in (?)', [
                RecordingInterface::STATUS_PENDING,
                RecordingInterface::STATUS_RUNNING
            ]);

        $start1 = new DateTime($object->getScheduledAt());
        $end1 = new DateTime($object->getScheduledAt());
        $end1->modify('+' . $object->getDuration() . ' minutes');

        $result = $this->getConnection()->fetchAll($select);
        foreach ($result as $row) {
            $timestamp = $row['started_at'] ?? $row['scheduled_at'];
            $start2 = new DateTime($timestamp);
            $end2 = new DateTime($timestamp);
            $end2->modify('+' . $row['duration'] . ' minutes');

            if ($start1 < $end2 && $end1 > $start2) {
                return true;
            }
        }
        return false;
    }
}
