<?php

class Krishinc_Mastergrouped_Block_Catalogsearch_List extends Mage_Catalog_Block_Product_List
{
    function isProductNew($product)
    {
        $newsFromDate = $product->getNewsFromDate();
        $newsToDate   = $product->getNewsToDate();
        if (!$newsFromDate && !$newToDate) {
            return false;
        }
        return Mage::app()->getLocale()
            ->isStoreDateInInterval($product->getStoreId(), $newsFromDate, $newsToDate);
    }
}
