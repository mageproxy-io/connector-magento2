<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\Component;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;

class Chart extends AbstractComponent
{
    const NAME = 'chart';

    private UrlInterface $url;

    public function __construct(
        ContextInterface $context,
        UrlInterface $url,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->url = $url;
    }

    public function getComponentName()
    {
        return self::NAME;
    }

    public function prepare()
    {
        parent::prepare();
        $updateUrl = $this->getData('config')['updateUrl'];
        $updateUrl = $this->url->getUrl($updateUrl);
        $this->setData(
            'config',
            array_replace_recursive(
                $this->getData('config'),
                [
                    'updateUrl' => $updateUrl
                ]
            )
        );
    }
}
