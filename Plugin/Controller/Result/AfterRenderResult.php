<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Plugin\Controller\Result;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;

class AfterRenderResult
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private CookieManagerInterface $cookieManager;

    /**
     * @var \Mageproxy\Connector\Api\RecordingManagerInterface
     */
    private RecordingManagerInterface $recordingManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private CookieMetadataFactory $cookieMetadataFactory;

    public function __construct(
        CookieManagerInterface $cookieManager,
        RecordingManagerInterface $recordingManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->recordingManager = $recordingManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result
    ) {
        $cookieName = 'mageproxy_recording';
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath('/');
        if ($this->recordingManager->isInProgress()) {
            $cookieLifetime = $this->recordingManager->getRunning()->getLifetime();
            if ($cookieLifetime > 0) {
                    $metadata->setDuration($cookieLifetime);
                $this->cookieManager->setPublicCookie(
                    $cookieName,
                    $this->recordingManager->getRunning()->getUuid(),
                    $metadata
                );
            }
        } elseif ($this->cookieManager->getCookie($cookieName)) {
            $this->cookieManager->deleteCookie($cookieName, $metadata);
        }
    }
}
