<?php

namespace Swissup\RefinedUrl\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Data extends AbstractHelper
{
    const XML_PATH_PRODUCT_URL_SHORTENED = 'swissup_refinedurl/product_url/shortened';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var int
     */
    protected $requestPathLength = null;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param UrlFinderInterface                        $urlFinder
     * @param Context                                   $context
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        UrlFinderInterface $urlFinder,
        Context $context
    ) {
        $this->resource = $resource;
        $this->urlFinder = $urlFinder;
        parent::__construct($context);
    }

    /**
     * Get max length for request_path field in table `url_rewrite`
     *
     * @return int
     */
    public function getRequestPathMaxLength()
    {
        if (!isset($this->requestPathLength)) {
            $fields = $this->resource->getConnection()->describeTable(
                $this->resource->getTableName('url_rewrite')
            );
            $requestPath = isset($fields['request_path'])
                ? $fields['request_path']
                : null;
            if ($requestPath && $requestPath['LENGTH']) {
                $this->requestPathLength = (int)$requestPath['LENGTH'];
            } else {
                $this->requestPathLength = 0;
            }
        }

        return $this->requestPathLength;
    }

    /**
     * Get product url sufix from Magento config
     *
     * @param  int    $storeId
     * @return string
     */
    public function getProductUrlSuffix($storeId)
    {
        return $this->scopeConfig->getValue(
            ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $requestPath
     * @param int $storeId
     * @return UrlRewrite|null
     */
    public function getRewrite($requestPath, $storeId)
    {
        return $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => ltrim($requestPath, '/'),
            UrlRewrite::STORE_ID => $storeId,
        ]);
    }

    /**
     * @return boolean
     */
    public function isShortenedProductUrlEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PRODUCT_URL_SHORTENED
        );
    }
}
