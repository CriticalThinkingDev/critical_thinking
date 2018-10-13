<?php
 
class Krishinc_Ajaxtocart_Block_Result extends Mage_CatalogSearch_Block_Result
{
	
    public function getProductListHtml()
    {
    	$product_list = $this->getLayout()->getBlock('search_result_list');
		$product_list->setTemplate('ajaxtocart/catalog/product/list.phtml'); 
        return $this->getChildHtml('search_result_list'); 
    }
    
      /**
     * Set search available list orders
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListOrders()
    {
        $category = Mage::getSingleton('catalog/layer')
            ->getCurrentCategory();
        /* @var $category Mage_Catalog_Model_Category */
        $availableOrders = $category->getAvailableSortByOptions();
       
		unset($availableOrders['position']);
        $availableOrders = array_merge(array(
            'relevance' => $this->__('Relevance')
        ), $availableOrders);

        $this->getListBlock()
            ->setAvailableOrders($availableOrders)
            ->setDefaultDirection('desc')
            ->setSortBy('relevance');  

//            $this->getListBlock()
//            ->setAvailableOrders($availableOrders)
//            ->setDefaultDirection('desc')
//            ->setSortBy('priority'); 


        return $this;
    }
    
}