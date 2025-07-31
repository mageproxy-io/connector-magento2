<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

/**
 * Simulates a recording that started
 */

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

Resolver::getInstance()->requireDataFixture('Mageproxy_Connector::Test/Integration/_files/recording_pending.php');

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$recordingRepository = $objectManager->create(RecordingRepositoryInterface::class);
$recording = $recordingRepository->get('pending');

$recording->setUuid('running');
$recording->setScheduledAt(
    (new DateTime())
        ->modify("-10 minutes")
        ->format('Y-m-d H:i:s')
);
$recordingManager = $objectManager->get(RecordingManagerInterface::class);
$recordingManager->start($recording);

$dependencyCollection = $objectManager->create(\Mageproxy\Connector\Model\ResourceModel\Dependency\Collection::class);
foreach (['cms_index_index', 'catalog_product_view'] as $pageHandle) {
    foreach ([
        'requirejs/require',
        'Foo_Bar/js/foo',
        'Foo_Baz/js/baz',
        'text!ui/templates/form.html'
     ] as $moduleId) {
        /** @var \Mageproxy\Connector\Model\Dependency $dependency */
        $dependency = $dependencyCollection->getNewEmptyItem();
        $dependency->setPageHandle($pageHandle);
        $dependency->setModuleId($moduleId);
        $dependency->setRecordingId((int) $recording->getId());
        $dependencyCollection->addItem($dependency);
    }
}

$dependencyCollection->save();
$recording->setDepsCnt(4);
$recordingRepository->save($recording);
