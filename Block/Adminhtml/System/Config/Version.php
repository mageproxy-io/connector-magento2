<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Mageproxy\Connector\Helper\Composer;
use Mageproxy\Connector\Helper\Github;

class Version extends Field
{
    /**
     * @var \Mageproxy\Connector\Helper\Composer
     */
    private Composer $composerHelper;

    /**
     * @var \Mageproxy\Connector\Helper\Github
     */
    private Github $githubHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mageproxy\Connector\Helper\Composer $composerHelper
     * @param \Mageproxy\Connector\Helper\Github $githubHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Composer $composerHelper,
        Github $githubHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->composerHelper = $composerHelper;
        $this->githubHelper = $githubHelper;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    // protected function _getElementHtml(AbstractElement $element)
    // {
    //     $element->setValue($this->composerHelper->getVersion());
    //     return parent::_getElementHtml($element);
    // }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $version = $this->composerHelper->getVersion();
        $element->setValue($version);

        try {
            $latest = $this->githubHelper->getLatestRelease();
            if ($latest !== null && version_compare($version, $latest, '<')) {
                $element->setComment("Newer version available: {$latest}");
            }
        } catch (\Exception $e) {
            // let's ignore this exception
        }

        return parent::render($element);

    }

}
