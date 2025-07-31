<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Asset;

use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Repository;

class RecorderJsAssetFactory
{
    public const FILE_IDENTIFIER = 'Mageproxy_Connector::js/requirejs/recorder.js';

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private Repository $assetRepo;

    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        Repository $assetRepo
    ) {
        $this->assetRepo = $assetRepo;
    }

    /**
     * @return \Magento\Framework\View\Asset\File
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(): File
    {
        return $this->assetRepo->createAsset(self::FILE_IDENTIFIER);
    }
}
