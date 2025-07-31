<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Magento\Framework\Model\AbstractModel;
use Mageproxy\Connector\Api\Data\DependencyInterface;

class Dependency extends AbstractModel implements DependencyInterface
{
    protected function _construct()
    {
        $this->_init(\Mageproxy\Connector\Model\ResourceModel\Dependency::class);
    }

    public function getModuleId(): ?string
    {
        return $this->getData(self::MODULE_ID);
    }

    public function setModuleId(string $moduleId): void
    {
        $this->setData(self::MODULE_ID, $moduleId);
    }

    public function getRecordingId(): ?int
    {
        return $this->getData(self::RECORDING_ID);
    }

    public function setRecordingId(int $recordingId): void
    {
        $this->setData(self::RECORDING_ID, $recordingId);
    }

    public function getPageHandle(): ?string
    {
        return $this->getData(self::PAGE_HANDLE);
    }

    public function setPageHandle(string $pageHandle)
    {
        $this->setData(self::PAGE_HANDLE, $pageHandle);
    }
}
