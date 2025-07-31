<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ResourceModel\Optimization\Template;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\Optimization\Template;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Template\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $this->createOptimizationTemplates();
        $objectManager = Bootstrap::getObjectManager();
        $collection = $objectManager->create(Collection::class);
        $collection->addFieldToFilter('store_id', 1);
        $collection->load();
        self::assertSame(1, $collection->count());
    }

    private function createOptimizationTemplates(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        // Create a default template and store 1 template
        for ($i = 0; $i < 2; $i++) {
            $template = $objectManager->create(Template::class);
            $template->setMinifyJs(true);
            $template->setMinifyHtml(true);
            $template->setExcludeDeps(['foo/bar']);
            $template->setHandles(['catalog_product_view', 'cms_index_index']);
            $template->setStoreId($i);
            $template->save();
        }
    }
}
