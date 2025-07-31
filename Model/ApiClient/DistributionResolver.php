<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ApiClient;

use Magento\Framework\App\ProductMetadataInterface;
use Mageproxy\Connector\Model\ApiClient\DistributionInterfaceFactory;

class DistributionResolver
{
    private ProductMetadataInterface $productMetadata;
    private DistributionInterfaceFactory $distributionFactory;

    public function __construct(
        ProductMetadataInterface $productMetadata,
        DistributionInterfaceFactory $distributionFactory
    ) {
        $this->productMetadata = $productMetadata;
        $this->distributionFactory = $distributionFactory;
    }

    public function resolve(): DistributionInterface
    {
        $distro = $this->distributionFactory->create();
        $distro->setEdition(strtolower($this->productMetadata->getEdition()));
        $parts = explode('-', $this->productMetadata->getVersion());
        $version = array_shift($parts);
        $distro->setVersion($version);
        if (!empty($parts)) {
            $distro->setRevision($parts[0]);
        }
        return $distro;
    }
}
