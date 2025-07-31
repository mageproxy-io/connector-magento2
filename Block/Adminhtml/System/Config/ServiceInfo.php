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
use Mageproxy\Connector\Model\ApiClient\GetServiceInterface;

class ServiceInfo extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Mageproxy\Connector\Model\ApiClient\GetServiceInterface
     */
    private GetServiceInterface $getService;

    public function __construct(
        Context $context,
        GetServiceInterface $getService,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->getService = $getService;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $serviceId = $element->getValue();
        if (!$serviceId) {
            return parent::render($element);
        }
        try {
            $result = $this->getService->execute($serviceId);
            if (!$result) {
                $element->setComment('Service not verified');
                return parent::render($element);
            }
            $comment = 'plan: %s [%s]</span>, mode: %s';
            $statusStr = $result->getStatus() === GetServiceInterface::STATUS_ACTIVE ? __('active') : __('pending');
            $element->setComment(sprintf($comment, $result->getPlan(), $statusStr, $result->getMode()));
        } catch (\Exception $e) {
            $element->setComment('Service not verified');
            // let's ignore this exception
        }

        return parent::render($element);
    }
}
