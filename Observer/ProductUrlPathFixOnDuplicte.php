<?php

namespace Swissup\RefinedUrl\Observer;

class ProductUrlPathFixOnDuplicte implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();
        if ($product->getIsDuplicate()) {
            // Unset url path attribute to prevent infinit loop on product copy.
            // Check \Magento\Catalog\Model\Product\Copier::80-92.
            $product->setUrlPath(null);
        }
    }
}
