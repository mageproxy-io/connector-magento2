<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Optimization;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface;
use Mageproxy\Connector\Model\Optimization\Template;
use PHPUnit\Framework\TestCase;

class TemplateRepositoryTest extends TestCase
{
    public function testItImplementsAServiceContract(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->create(OptimizationTemplateRepositoryInterface::class);
    }

    public function testGetList(): void
    {
        $this->createOptimizationTemplates();
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->create(OptimizationTemplateRepositoryInterface::class);
        $searchResults = $repository->getList($objectManager->create(SearchCriteriaInterface::class));
        self::assertIsArray($searchResults->getItems());
        self::assertSame(2, count($searchResults->getItems()));
    }

    public function testGetById(): void
    {
        $this->createOptimizationTemplates();
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->create(OptimizationTemplateRepositoryInterface::class);
        $searchResults = $repository->getList($objectManager->create(SearchCriteriaInterface::class));
        $items = $searchResults->getItems();
        $template = array_shift($items);
        $templateId = $template->getId();
        $template = $repository->getById((int) $templateId);
        self::assertSame($templateId, $template->getId());
    }

    public function testSave(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->create(OptimizationTemplateRepositoryInterface::class);
        $template = $objectManager->create(Template::class);
        $template->setMinifyJs(true);
        $template->setMinifyHtml(true);
        $template->setExcludeDeps(['foo/bar']);
        $template->setHandles(['catalog_product_view', 'cms_index_index']);
        $template->setStoreId(1);
        $repository->save($template);
        $template = $repository->getById((int) $template->getId());
        self::assertSame(1, $template->getStoreId());
    }

    public function testDeleteById(): void
    {
        $this->createOptimizationTemplates();
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->create(OptimizationTemplateRepositoryInterface::class);
        $searchResults = $repository->getList($objectManager->create(SearchCriteriaInterface::class));
        $items = $searchResults->getItems();
        $template = array_shift($items);
        $templateId = $template->getId();
        $repository->deleteById((int) $templateId);
        $this->expectException(\Magento\Framework\Exception\NoSuchEntityException::class);
        $repository->getById((int) $templateId);
    }

    public function testDelete(): void
    {
        $this->createOptimizationTemplates();
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->create(OptimizationTemplateRepositoryInterface::class);
        $searchResults = $repository->getList($objectManager->create(SearchCriteriaInterface::class));
        $items = $searchResults->getItems();
        $template = array_shift($items);
        $templateId = $template->getId();
        $repository->delete($template);
        $this->expectException(\Magento\Framework\Exception\NoSuchEntityException::class);
        $repository->getById((int) $templateId);

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
