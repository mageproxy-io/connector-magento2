<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Asset;

use Magento\Framework\View\Asset\PropertyGroupFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\Asset\PropertyGroupArrayManager;
use PHPUnit\Framework\TestCase;

class PropertyGroupArrayManagerTest extends TestCase
{
    /**
     * @var \Mageproxy\Connector\Model\Asset\PropertyGroupArrayManager|mixed
     */
    private $propertyGroupManager;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->propertyGroupManager = $objectManager->get(PropertyGroupArrayManager::class);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testItMovesTheAssetAfterTheTargetAssetWhenSourceAndTargetInTheSameGroup(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $assetRepository = $objectManager->get(Repository::class);
        $groups = $this->buildPropertyGroupsFromStructure([
            [
                'assets' => [
                    'mage/calendar.css',
                    'css/styles-m.css',
                    'mage/gallery/gallery.css',
                ],
                'properties' => [
                    'content_type' => 'css'
                ]
            ],
            [
                'assets' => [
                    'foo/bar.js',
                    'mage/gallery/gallery.js',
                    'Foo_Bar::js/baz.js',
                ],
                'properties' => [
                    'content_type' => 'js'
                ]
            ]
        ]);

        $this->propertyGroupManager->move(
            $assetRepository->createAsset('Foo_Bar::js/baz.js'),
            $assetRepository->createAsset('foo/bar.js'),
            $groups
        );

        $ids = $this->extractAssets($groups);
        $this->assertEquals(
            [
                'mage/calendar.css',
                'css/styles-m.css',
                'mage/gallery/gallery.css',
                'foo/bar.js',
                'Foo_Bar::js/baz.js',
                'mage/gallery/gallery.js'
            ],
            $ids
        );
    }

    /**
     * @magentoAppArea frontend
     */
    public function testItMovesTheAssetAfterTheTargetAssetWhenSourceAndTargetInDifferentGroups(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $assetRepo = $objectManager->get(Repository::class);
        $groups = $this->buildPropertyGroupsFromStructure([
            [
                'assets' => ['mage/calendar.css', 'css/styles-m.css', 'mage/gallery/gallery.css'],
                'properties' => ['content_type' => 'css']
            ],
            [
                'assets' => ['foo/bar.js'],
                'properties' => [ 'content_type' => 'js', 'prop1' => 'val1']
            ],
            [
                'assets' => ['mage/gallery/gallery.js'],
                'properties' => ['content_type' => 'js', 'prop2' => 'val2']
            ],
            [
                'assets' => ['Foo_Bar::js/baz.js'],
                'properties' => ['content_type' => 'js', 'prop3' => 'val3']
            ],
        ]);

        $this->propertyGroupManager->move(
            $assetRepo->createAsset('Foo_Bar::js/baz.js'),
            $assetRepo->createAsset('foo/bar.js'),
            $groups
        );
        $ids = $this->extractAssets($groups);

        $this->assertEquals(
            [
                'mage/calendar.css',
                'css/styles-m.css',
                'mage/gallery/gallery.css',
                'foo/bar.js',
                'Foo_Bar::js/baz.js',
                'mage/gallery/gallery.js'
            ],
            $ids
        );
    }

    /**
     * @magentoAppArea frontend
     */
    public function testThePropertyGroupsAreUnchangedWhenTheAssetIsNotPresent(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $assetRepo = $objectManager->get(Repository::class);
        $groups = $this->buildPropertyGroupsFromStructure([
            [
                'assets' => ['mage/calendar.css', 'css/styles-m.css', 'mage/gallery/gallery.css'],
                'properties' => ['content_type' => 'css']
            ],
            [
                'assets' => ['foo/bar.js'],
                'properties' => ['content_type' => 'js']
            ],
            [
                'assets' => ['mage/gallery/gallery.js'],
                'properties' => ['content_type' => 'js']
            ],
            [
                'assets' => ['Foo_Bar::js/baz.js'],
                'properties' => ['content_type' => 'js']
            ],
        ]);

        $this->propertyGroupManager->move(
            $assetRepo->createAsset('Foo_Bar::js/baz.js'),
            $assetRepo->createAsset('foo/bar2.js'),
            $groups
        );
        $ids = $this->extractAssets($groups);

        $this->assertEquals(
            [
                'mage/calendar.css',
                'css/styles-m.css',
                'mage/gallery/gallery.css',
                'foo/bar.js',
                'mage/gallery/gallery.js',
                'Foo_Bar::js/baz.js'
            ],
            $ids
        );
    }

    /**
     * @magentoAppArea frontend
     */
    public function testItRemovesTheAssetAndTheGroupWhenTheGroupHasOnlyOneAsset(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $assetRepository = $objectManager->get(Repository::class);
        $groups = $this->buildPropertyGroupsFromStructure([
            [
                'assets' => [
                    'foo/bar.js'
                ],
                'properties' => [
                    'content_type' => 'js'
                ]
            ]
        ]);

        $this->propertyGroupManager->delete($assetRepository->createAsset('foo/bar.js'), $groups);
        $this->assertEmpty($groups);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testItRemovesOnlyTheAssetWhenTheGroupHasMoreThanOneAsset(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $assetRepository = $objectManager->get(Repository::class);
        $groups = $this->buildPropertyGroupsFromStructure([
            [
                'assets' => [
                    'foo/bar.js',
                    'foo/baz.js'
                ],
                'properties' => [
                    'content_type' => 'js'
                ]
            ]
        ]);

        $this->propertyGroupManager->delete($assetRepository->createAsset('foo/bar.js'), $groups);
        $this->assertCount(1, $groups);
        $this->assertCount(1, $groups[0]->getAll());
        $this->assertArrayHasKey('foo/baz.js', $groups[0]->getAll());
        $this->assertArrayNotHasKey('foo/bar.js', $groups[0]->getAll());
    }

    private function buildPropertyGroupsFromStructure(array $structure): array
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $design = $objectManager->get(DesignInterface::class);
        $design->setDesignTheme('Magento/luma');
        $assetRepository = $objectManager->get(Repository::class);
        $propertyGroupFactory = $objectManager->get(PropertyGroupFactory::class);
        $groups = [];
        foreach ($structure as $groupStructure) {
            $group = $propertyGroupFactory->create(['properties' => $groupStructure['properties']]);
            foreach ($groupStructure['assets'] as $assetPath) {
                $group->add($assetPath, $assetRepository->createAsset($assetPath));
            }
            $groups[] = $group;
        }
        return $groups;
    }

    private function extractAssets(array $groups): array
    {
        $assets = [];
        foreach ($groups as $group) {
            $assets = array_merge($assets, array_keys($group->getAll()));
        }
        return $assets;
    }
}
