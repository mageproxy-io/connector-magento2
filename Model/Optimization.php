<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Mageproxy\Connector\Api\Data\OptimizationBundleInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationBundleRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Collection;

/**
 * @method setTemplateId(int $templateId)
 * @method getTemplateId(): ?int
 */
class Optimization extends AbstractModel implements OptimizationInterface
{
    /** For event handlers */
    /** @var string  */
    protected $_eventObject = 'optimization';
    /** @var string  */
    protected $_eventPrefix = 'mageproxy_optimization';

    /**
     * @var \Mageproxy\Connector\Api\Data\RecordingInterface|null
     */
    private ?RecordingInterface $recording = null;

    /**
     * @var array|null
     */
    private ?array $bundles = null;

    /**
     * @var \Mageproxy\Connector\Api\RecordingRepositoryInterface
     */
    private RecordingRepositoryInterface $recordingRepository;

    /**
     * @var \Mageproxy\Connector\Api\OptimizationBundleRepositoryInterface
     */
    private OptimizationBundleRepositoryInterface $bundleRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Optimization::class);
    }

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageproxy\Connector\Model\ResourceModel\Optimization $resource
     * @param \Mageproxy\Connector\Model\ResourceModel\Optimization\Collection $resourceCollection
     * @param \Mageproxy\Connector\Api\RecordingRepositoryInterface $recordingRepository
     * @param \Mageproxy\Connector\Api\OptimizationBundleRepositoryInterface $bundleRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Optimization $resource,
        Collection $resourceCollection,
        RecordingRepositoryInterface $recordingRepository,
        OptimizationBundleRepositoryInterface $bundleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->recordingRepository = $recordingRepository;
        $this->bundleRepository = $bundleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getUuid(): ?string
    {
        return $this->getData(self::UUID);
    }

    /**
     * @inheritdoc
     */
    public function setUuid(string $uuid): void
    {
        $this->setData(self::UUID, $uuid);
    }

    /**
     * @inheritdoc
     */
    public function setRecordingId(int $recordingId): void
    {
        $this->setData(self::RECORDING_ID, $recordingId);
    }

    /**
     * @inheritdoc
     */
    public function getRecordingId(): ?int
    {
        $id = $this->getData(self::RECORDING_ID);
        return $id === null ? null : (int) $id;
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): int
    {
        return (int) $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(int $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getRequestedAt(): string
    {
        return $this->getData(self::REQUESTED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setRequestedAt(string $requestedAt): void
    {
        $this->setData(self::REQUESTED_AT, $requestedAt);
    }

    /**
     * @inheritdoc
     */
    public function getMinifyJs(): bool
    {
        return (bool) $this->getData(self::MINIFY_JS);
    }

    /**
     * @inheritdoc
     */
    public function setMinifyJs(bool $minifyJs): void
    {
        $this->setData(self::MINIFY_JS, $minifyJs);
    }

    /**
     * @inheritdoc
     */
    public function getMinifyHtml(): bool
    {
        return (bool) $this->getData(self::MINIFY_HTML);
    }

    /**
     * @inheritdoc
     */
    public function setMinifyHtml(bool $minifyHtml): void
    {
        $this->setData(self::MINIFY_HTML, $minifyHtml);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId(): int
    {
        return (int) $this->getData(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId(int $storeId): void
    {
        $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getExcludeDeps(): array
    {
        return $this->getData(self::EXCLUDE_DEPS);
    }

    /**
     * @inheritdoc
     */
    public function setExcludeDeps(?array $excludeDeps): void
    {
        $this->setData(self::EXCLUDE_DEPS, $excludeDeps);
    }

    /**
     * @inheritdoc
     */
    public function getHandles(): ?array
    {
        return $this->getData(self::HANDLES);
    }

    /**
     * @inheritdoc
     */
    public function setHandles(?array $handles): void
    {
        $this->setData(self::HANDLES, $handles);
    }

    /**
     * @inheritdoc
     */
    public function getDepsCount(): int
    {
        return (int) $this->getData(self::DEPS_CNT);
    }

    /**
     * @inheritdoc
     */
    public function setDepsCount(int $depsCount): void
    {
        $this->setData(self::DEPS_CNT, $depsCount);
    }

    /**
     * @inheritdoc
     */
    public function getDeployedAt(): ?string
    {
        return $this->getData(self::DEPLOYED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setDeployedAt(?string $deployedAt): void
    {
        $this->setData(self::DEPLOYED_AT, $deployedAt);
    }

    /**
     * @inheritdoc
     */
    public function getRequestedBy(): string
    {
        return $this->getData(self::REQUESTED_BY);
    }

    /**
     * @inheritdoc
     */
    public function setRequestedBy(string $requestedBy): void
    {
        $this->setData(self::REQUESTED_BY, $requestedBy);
    }

    /**
     * @inheritdoc
     */
    public function getRemoveDeps(): ?array
    {
        return $this->getData(self::REMOVE_DEPS);
    }

    /**
     * @inheritdoc
     */
    public function setRemoveDeps(?array $removeDeps): void
    {
        $this->setData(self::REMOVE_DEPS, $removeDeps);
    }

    /**
     * @inheritdoc
     */
    public function setBrowserslistConfig(?string $browserslistConfig): void
    {
        $this->setData(self::BROWSERSLIST_CONFIG, $browserslistConfig);
    }

    /**
     * @inheritdoc
     */
    public function getBrowserslistConfig(): ?string
    {
        return $this->getData(self::BROWSERSLIST_CONFIG);
    }

    /**
     * @inheritdoc
     */
    public function setTranspileGlobs(?array $transpileGlobs): void
    {
        $this->setData(self::TRANSPILE_GLOBS, $transpileGlobs);
    }

    /**
     * @inheritdoc
     */
    public function getTranspileGlobs(): ?array
    {
        return $this->getData(self::TRANSPILE_GLOBS);
    }

    /**
     * @inheritdoc
     */
    public function setErrorMessage(?string $errorMessage): void
    {
        $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage(): ?string
    {
        return $this->getData(self::ERROR_MESSAGE);
    }

    /**
     * @return \Mageproxy\Connector\Api\Data\RecordingInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRecording(): ?RecordingInterface
    {
        if ($this->recording === null && $this->getRecordingId() !== null) {
            $this->recording = $this->recordingRepository->getById($this->getRecordingId());
        }
        return $this->recording;
    }

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return void
     */
    public function setRecording(RecordingInterface $recording): void
    {
        $this->setRecordingId((int) $recording->getId());
        $this->recording = $recording;
    }

    /**
     * @return \Mageproxy\Connector\Api\Data\OptimizationBundleInterface[]|null
     */
    public function getBundles(): ?array
    {
        if ($this->bundles === null) {
            $sortBySize  = $this->sortOrderBuilder
                ->setField(OptimizationBundleInterface::COMPRESSED_SIZE)
                ->setDescendingDirection()
                ->create();
            $items = $this->bundleRepository->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(OptimizationInterface::ID, $this->getId())
                    ->addSortOrder($sortBySize)
                    ->create()
            )->getItems();
            $this->bundles = array_values($items);
        }
        return $this->bundles;
    }

    public function getHdlsCount(): int
    {
        return (int) $this->getData(self::HDLS_CNT);
    }

    public function setHdlsCount(int $hdlsCnt): void
    {
        $this->setData(self::HDLS_CNT, $hdlsCnt);
    }

    public function getUsePolyfills(): bool
    {
        return (boolean) $this->getData(self::USE_POLYFILLS);
    }

    public function setUsePolyfills(bool $usePolyfills): void
    {
        $this->setData(self::USE_POLYFILLS, $usePolyfills);
    }

    public function getRecordingChecksum(): ?string
    {
        return $this->getData(self::RECORDING_CHECKSUM);
    }

    public function setRecordingChecksum(?string $snapshot): void
    {
        $this->setData(self::RECORDING_CHECKSUM, $snapshot);
    }

    public function getRevertedAt(): ?string
    {
        return $this->getData(self::REVERTED_AT);
    }

    public function setRevertedAt(string $revertedAt): void
    {
        $this->setData(self::REVERTED_AT, $revertedAt);
    }

    public function getChunkJs(): bool
    {
        return (bool) $this->getData(self::CHUNK_JS);
    }

    public function setChunkJs(bool $chunkJs): void
    {
        $this->setData(self::CHUNK_JS, $chunkJs);
    }

    public function getChunkJsSize(): int
    {
        $size = $this->getData(self::CHUNK_JS_SIZE);
        return $size === null ? 0 : (int) $size;
    }

    public function setChunkJsSize(int $chunkJsSize): void
    {
        $this->setData(self::CHUNK_JS_SIZE, $chunkJsSize);
    }

    public function getIncludeSourceMapJs(): bool
    {
        return (bool) $this->getData(self::INCLUDE_SOURCEMAP_JS);
    }

    public function setIncludeSourceMapJs(bool $includeSourceMapJs): void
    {
        $this->setData(self::INCLUDE_SOURCEMAP_JS, $includeSourceMapJs);
    }
}
