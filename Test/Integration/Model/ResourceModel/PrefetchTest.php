<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ResourceModel;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\Prefetch;
use Mageproxy\Connector\Model\ResourceModel\Prefetch as PrefetchResource;
use PHPUnit\Framework\TestCase;

class PrefetchTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/prefetch.php
     */
    public function testSaveAndLoad(): void
    {
        $om = Bootstrap::getObjectManager();
        /** @var PrefetchResource $resource */
        $resource = $om->get(PrefetchResource::class);

        // Load fixture record by store_id
        /** @var Prefetch $model */
        $model = $om->create(Prefetch::class);
        $resource->load($model, 1, 'store_id');
        self::assertNotNull($model->getId());
        self::assertSame(1, (int)$model->getData('store_id'));
        $rules = $model->getData('rules');
        self::assertIsArray($rules);
        self::assertSame('bundle-a', $rules[0]['bundle_pattern']);

        // Update and save
        $rules[] = ['selector' => 'new', 'bundle_pattern' => 'bundle-new', 'prefetch_on' => 'interaction'];
        $model->setData('rules', $rules);
        $resource->save($model);

        $reloaded = $om->create(Prefetch::class);
        $resource->load($reloaded, (int)$model->getId());
        $reloadedRules = $reloaded->getData('rules');
        self::assertIsArray($reloadedRules);
        self::assertSame('bundle-new', $reloadedRules[count($reloadedRules)-1]['bundle_pattern']);
    }
}
