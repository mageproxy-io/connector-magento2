<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\DataProvider\Listing;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageproxy\Connector\Model\ResourceModel\Optimization;
use Psr\Log\LoggerInterface as Logger;

class OptimizationDataProvider extends SearchResult
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private RequestInterface $request;

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        $mainTable = Optimization::MAIN_TABLE_NAME,
        $resourceModel = Optimization::class,
        $identifierName = Optimization::MAIN_TABLE_PRIMARY_KEY
    ) {
        $this->request = $request;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $recordingId = $this->request->getParam('recording_id', false);
        if ($recordingId) {
            $this->getSelect()->where('recording_id=?', $recordingId);
        }
        return $this;
    }
}
