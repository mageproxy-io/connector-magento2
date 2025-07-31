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
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class MagentoDistributionResolverTest extends TestCase
{
    private $objectManager;
    private $productMetadataMock;
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productMetadataMock = $this->createMock(ProductMetadataInterface::class);
        $this->sut = $this->objectManager->create(DistributionResolver::class, [
            'productMetadata' => $this->productMetadataMock
        ]);
    }

    public function testResolverWithRevision()
    {
        $this->productMetadataMock
            ->expects(self::once())
            ->method('getEdition')
            ->willReturn('Community');
        $this->productMetadataMock
            ->expects(self::once())
            ->method('getVersion')
            ->willReturn('2.4.6-p2');

        $distribution = $this->sut->resolve();

        self::assertSame('community', $distribution->getEdition());
        self::assertSame('2.4.6', $distribution->getVersion());
        self::assertSame('p2', $distribution->getRevision());
    }

    public function testResolverWithoutRevision()
    {
        $this->productMetadataMock
            ->expects(self::once())
            ->method('getEdition')
            ->willReturn('Enterprise');
        $this->productMetadataMock
            ->expects(self::once())
            ->method('getVersion')
            ->willReturn('2.4.5');

        $distribution = $this->sut->resolve();

        self::assertSame('enterprise', $distribution->getEdition());
        self::assertSame('2.4.5', $distribution->getVersion());
        self::assertNull($distribution->getRevision());
    }


}
