<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\DataProvider\Form;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\Optimization;
use Mageproxy\Connector\Model\Optimization\Source\Status as StatusOptions;
use Mageproxy\Connector\Model\ResourceModel\Optimization\CollectionFactory;

class OptimizationViewDataProvider extends AbstractDataProvider
{
    protected array $loadedData;
    private StatusOptions $statusOptions;
    private UrlInterface $urlBuilder;
    private RequestInterface $request;
    private OptimizationRepositoryInterface $optimizationRepository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StatusOptions $statusOptions,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        OptimizationRepositoryInterface $optimizationRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->loadedData = [];
        $this->collection = $collectionFactory->create();
        $this->statusOptions = $statusOptions;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->optimizationRepository = $optimizationRepository;
    }

    public function getData()
    {
        if ($this->loadedData) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $item) {
            /** @var  \Mageproxy\Connector\Model\Optimization $item */
            $data = $item->getData();
            $data['status'] = $this->statusOptions->getLabel((int) $data[OptimizationInterface::STATUS]);
            $data['exclude_deps'] = $this->prepareData($item->getExcludeDeps(), 'module_id');
            $data['remove_deps'] = $this->prepareData($item->getRemoveDeps(), 'module_id');
            $data['handles'] = $this->prepareData($item->getHandles(), 'page_handle');
            $data['transpile_globs'] = $this->prepareData($item->getTranspileGlobs(), 'glob');
            $data['template_url'] = $this->getTemplateUrl((int) $item->getTemplateId());
            $data['chunk_js_label'] = (int) $item->getChunkJs() ? __('Yes') : __('No');
            $data['chunk_js_size_label'] = $item->getChunkJsSize() . ' (kB)';
            $data['minify_js_label'] = (int) $item->getMinifyJs() ? __('Yes') : __('No');
            $data['minify_html_label'] = (int) $item->getMinifyHtml() ? __('Yes') : __('No');
            $data['use_polyfills_label'] = (int) $item->getUsePolyfills() ? __('Yes') : __('No');
            $data['include_sourcemap_js_label'] = (int) $item->getIncludeSourcemapJs() ? __('Yes') : __('No');
            $this->loadedData[$item->getId()] = $data;
        }

        return $this->loadedData;
    }

    private function prepareData($data, $key): array
    {
        return array_map(function ($item) use ($key) {
            return [ $key => $item ];
        }, $data);
    }

    private function getTemplateUrl(int $templateId): string
    {
        $link = $this->urlBuilder->getUrl('mageproxy/template/index', ['id' => $templateId]);
        return sprintf('<a href="%s">%s</a>', $link, __('View'));
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $optimization = $this->getCurrentOptimization();
        if (!$optimization->getTemplateId()) {
            $meta['general_fs'] = [
                'children' => [
                    'template_url' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentDisabled' => true
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        if ($optimization->getRemoveDeps()) {
            $meta = $this->removeEmptyMessage($meta, 'remove_deps_fs');
        }
        if ($optimization->getExcludeDeps()) {
            $meta = $this->removeEmptyMessage($meta, 'exclude_deps_fs');
        }
        if ($optimization->getHandles()) {
            $meta = $this->removeEmptyMessage($meta, 'page_handles_fs');
        }
        if ($optimization->getTranspileGlobs()) {
            $meta = $this->removeEmptyMessage($meta, 'transpile_fs');
        } else {
            $meta['transpile_fs'] = [
                'children' => [
                    'browserslist_config' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentDisabled' => true
                                ]
                            ]
                        ]
                    ],
                    'use_polyfills_label' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentDisabled' => true
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        if (!$optimization->getChunkJs()) {
            $meta['chunking_fs'] = [
                'children' => [
                    'chunk_js_size_label' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentDisabled' => true
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        return $meta;
    }

    private function getCurrentOptimization(): Optimization
    {
        $optimizationId = $this->request->getParam($this->getRequestFieldName());
        return $this->optimizationRepository->getById((int) $optimizationId);
    }

    private function removeEmptyMessage(array $meta, string $field): array
    {
        $meta[$field] = [
            'children' => [
                'no_items_message' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentDisabled' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $meta;
    }
}
