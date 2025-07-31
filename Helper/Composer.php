<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Helper;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\Serializer\Json as Serializer;

class Composer
{
    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $filesystem;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        Reader $moduleReader,
        File $filesystem,
        Serializer $serializer
    ) {
        $this->moduleReader = $moduleReader;
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getComposerJsonContentsAsArray(): array
    {
        $dir = $this->moduleReader->getModuleDir('', 'Mageproxy_Connector');
        $file = $dir . '/composer.json';
        $composerJson = $this->filesystem->fileGetContents($file);
        return $this->serializer->unserialize($composerJson);

    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        try {
        return $this->getComposerJsonContentsAsArray()['version'] ?? null;
        } catch (FileSystemException $e) {
            return null;
        }
    }
}
