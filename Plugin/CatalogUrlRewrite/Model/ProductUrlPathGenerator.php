<?php

namespace Swissup\RefinedUrl\Plugin\CatalogUrlRewrite\Model;

use Magento\Framework\Exception\LocalizedException;

class ProductUrlPathGenerator
{
    /**
     * @var \Swissup\RefinedUrl\Helper\Data
     */
    private $helper;

    /**
     * @param \Swissup\RefinedUrl\Helper\Data $helper
     */
    public function __construct(
        \Swissup\RefinedUrl\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Check max allowed url length
     *
     * @param  \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $subject
     * @param  string                                                   $result
     * @param  \Magento\Catalog\Model\Product                           $product
     * @param  int                                                      $storeId
     * @param  \Magento\Catalog\Model\Category|null                     $category
     * @return string
     */
    public function afterGetUrlPathWithSuffix(
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $subject,
        $result,
        \Magento\Catalog\Model\Product $product,
        $storeId,
        \Magento\Catalog\Model\Category $category = null
    ) {
        $max = $this->helper->getRequestPathMaxLength();
        if ($max && strlen($result) > $max) {
            $shortedPath = null;
            if ($this->helper->isProductUrlShortedEnabled()) {
                // try to generate shorted path to fit it in database field
                $shortedPath = $this->generateShortedPath($result, $storeId);
            }

            if ($shortedPath === null) {
                // unable to generate shorted url or it is disable
                throw new LocalizedException(
                    __(
                        "<b>Generated URL is too long!</b>\nEntity type - \"%1\".\nEntity ID - \"%2\".<br />\nGenerated URL path \"%3\" has %4 characters.\nMax allowed length is %5.",
                        'product',
                        $product->getId(),
                        $result,
                        strlen($result),
                        $max
                    )
                );
            }

            return $shortedPath;
        }

        return $result;
    }

    /**
     * Generate shorted out url path from original one
     *
     * @param  string      $originalPath
     * @param  int         $storeId
     * @return string|null
     */
    public function generateShortedPath($originalPath, $storeId)
    {
        $urlSufix = $this->helper->getProductUrlSuffix($storeId);
        $length = $this->helper->getRequestPathMaxLength();
        $appendSufix = false;
        if ($urlSufix
            && substr($originalPath, -strlen($urlSufix)) === $urlSufix // ends with $urlSufix
        ) {
            $length -= strlen($urlSufix);
            $appendSufix = true;
        }

        $i = 0;
        do {
            $i++;
            $shortedPath = substr($originalPath, 0, $length - 1 - strlen((string)$i))
                . '~'
                . (string)$i
                . ($appendSufix ? $urlSufix : '');
            // check if exists rewrite for shorted path
            if ($this->helper->getRewrite($shortedPath, $storeId)) {
                $shortedPath = '';
            }
        } while (empty($shortedPath) && $i <= 99);

        return empty($shortedPath) ? null : $shortedPath;
    }
}
