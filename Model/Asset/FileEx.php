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

use Magento\Framework\View\Asset\ContextInterface;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Minification;
use Magento\Framework\View\Asset\Source;

/**
 * Extend the File asset to store additional ephemeral information.
 */
class FileEx extends File
{
    /**
     * @var Minification
     */
    private $minification;

    /**
     * If this value is true, we skip minification even if enabled in the configuration.
     *
     * @var bool
     */
    protected $skipMinification = false;

    /**
     * @param Source $source
     * @param ContextInterface $context
     * @param string $filePath
     * @param string $module
     * @param string $contentType
     * @param Minification $minification
     */
    public function __construct(
        Source $source,
        ContextInterface $context,
        $filePath,
        $module,
        $contentType,
        Minification $minification
    ) {
        $this->minification = $minification;
        parent::__construct(
            $source,
            $context,
            $filePath,
            $module,
            $contentType,
            $minification
        );
    }

    /**
     * Get the value of skipMinification
     *
     * @return bool
     */
    public function getSkipMinification(): bool
    {
        return $this->skipMinification;
    }

    /**
     * Set the value of skipMinification
     *
     * @param bool $skipMinification
     * @return self
     */
    public function setSkipMinification(bool $skipMinification): self
    {
        $this->skipMinification = $skipMinification;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        $result = '';
        $result = $this->join($result, $this->context->getPath());
        $result = $this->join($result, $this->module);
        $result = $this->join($result, $this->filePath);
        if (!$this->skipMinification) {
            $result = $this->minification->addMinifiedSign($result);
        }
        return $result;
    }

    /**
     * Subroutine for building path
     *
     * @param string $path
     * @param string $item
     * @return string
     */
    private function join($path, $item)
    {
        return trim($path . ($item ? '/' . $item : ''), '/');
    }
}
