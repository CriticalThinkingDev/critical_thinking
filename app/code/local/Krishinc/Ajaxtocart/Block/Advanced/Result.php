<?php
 
class Krishinc_Ajaxtocart_Block_Advanced_Result extends Mage_CatalogSearch_Block_Advanced_Result
{
	
    public function getProductListHtml()
    {
    	$product_list = $this->getLayout()->getBlock('search_result_list');
		$product_list->setTemplate('ajaxtocart/catalog/product/list.phtml'); 
        return $this->getChildHtml('search_result_list');
    }
}