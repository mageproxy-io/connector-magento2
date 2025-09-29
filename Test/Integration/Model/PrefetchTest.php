<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\PrefetchInterface;
use Mageproxy\Connector\Model\Prefetch;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Model\Prefetch
 */
class PrefetchTest extends TestCase
{
    public function testUsesCorrectResourceModel(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Prefetch $model */
        $model = $objectManager->create(Prefetch::class);
        self::assertSame(\Mageproxy\Connector\Model\ResourceModel\Prefetch::class, $model->getResourceName());
    }

    public function testGetAndSetStoreIdAndRules(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Prefetch $model */
        $model = $objectManager->create(PrefetchInterface::class);
        $model->setData('store_id', 1);
        $rules = [
            [
                'selector' => 'a[href]',
                'bundle_pattern' => 'catalog.product.view',
                'prefetch_on' => 'interaction'
            ]
        ];
        $model->setRules($rules);
        $model->save();
        $rulesAfterSave = $model->getRules();
        self::assertSame(1, (int)$model->getData('store_id'));
        self::assertSame($rules, $rulesAfterSave);
    }
}
