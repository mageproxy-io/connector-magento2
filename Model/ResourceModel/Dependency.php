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

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mageproxy\Connector\Api\Data\DependencyInterface;

class Dependency extends AbstractDb
{
    const MAIN_TABLE_NAME = 'mageproxy_dependency';
    const MAIN_TABLE_PRIMARY_KEY = 'dependency_id';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_PRIMARY_KEY);
    }

    /**
     * @param array $depsByHandle
     * @param int $recordingId
     * @param string[] $pageHandlePriority
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertDepsByHandle(array $depsByHandle, int $recordingId, array $pageHandlePriority): void
    {
        $data = [];
        foreach ($depsByHandle as $handle => $deps) {
            foreach ($deps as $moduleId) {
                $depData = [
                    'page_handle' => $handle,
                    'module_id' => $moduleId,
                    'recording_id' => $recordingId
                ];
                $priority = array_search($handle, $pageHandlePriority, true);
                if ($priority !== false) {
                    $priority++; // increasing priority starting from 1
                    $depData['priority'] = $priority;
                } else {
                    $depData['priority'] = 0;
                }
                $data[] = $depData;
            }
        }
        $connection = $this->getConnection();
        // INSERT IGNORE for better performance
        $connection->insertArray(
            $this->getMainTable(),
            [
                DependencyInterface::PAGE_HANDLE,
                DependencyInterface::MODULE_ID,
                DependencyInterface::RECORDING_ID,
                'priority'
            ],
            $data,
            AdapterInterface::INSERT_IGNORE
        );
    }

    public function getUniqueDependencyCount($recordingId): int
    {
        $connection = $this->getConnection();
        return (int) $connection->fetchOne(
            $connection->select()
                ->from(
                    $this->getMainTable(),
                    ['deps_count' => new \Zend_Db_Expr('COUNT(DISTINCT module_id)')]
                )->where('recording_id = ?', $recordingId)
        );
    }
}
