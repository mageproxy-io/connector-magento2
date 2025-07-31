<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\Component\Listing\Column;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Mageproxy\Connector\Model\ResourceModel\Dependency\CollectionFactory;

class PageHandle implements OptionSourceInterface
{
    private CollectionFactory $dependencyCollectionFactory;
    private RequestInterface $request;

    public function __construct(
        CollectionFactory $dependencyCollectionFactory,
        RequestInterface $request
    ) {
        $this->dependencyCollectionFactory = $dependencyCollectionFactory;
        $this->request = $request;
    }

    public function toOptionArray()
    {
        $recordingId = $this->request->getParam('recording_id');
        $collection = $this->dependencyCollectionFactory->create();
        $select = $collection->getSelect();
        $select->reset();
        $select->distinct();
        $select->from($collection->getMainTable(), ['page_handle']);
        if ($recordingId) {
            $select->where('recording_id = ?', $recordingId);
        }
        $options = [];
        /** @var \Mageproxy\Connector\Api\Data\DependencyInterface $dependency */
        foreach ($collection as $dependency) {
            $options[] = [
                'label' => $dependency->getPageHandle(),
                'value' => $dependency->getPageHandle()
            ];
        }
        return $options;
    }
}
