<?php

class Krishinc_Catalogue_Model_Adminhtml_Observer
{
	 
	
	public function adminhtmlSalesOrderCreateAddCatalogueProduct()
	{
		
		$helper = Mage::helper('catalogue');
		if(!$helper->config('enabled'))
		{
			return;			 
		}
		if($catalogueItem = $helper->getCatalogueProductFromCurrentMonth())
		{	
	 
			$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$catalogueItem);  
 			$cart  =   $this->_getOrderCreateModel();
 			$quote = $this->_getQuote();
			$totalQuoteItem = count($quote->getAllItems()); 
			if($totalQuoteItem > 0) {
				 
			    $isCatalogueAdded = false;
			    foreach($quote->getAllItems() as $item) {   
			    	if($item->getCatalogueItem())
			    	{ 
			    		if($item->getSku() != $catalogueItem)
			    		{
			    			$cart->removeQuoteItem($item->getId());  
			    		}
			    	}
			    	if($item->getSku() == $catalogueItem)
			    	{
			    		$isCatalogueAdded  = true;
			    		
			    	}
			    	 if($totalQuoteItem == 1){$catalogueItemIdToRemove = $item->getId();}
			    }
		        if(($totalQuoteItem == 1) && ($isCatalogueAdded == true))
			    {
			    	$cart->removeQuoteItem($catalogueItemIdToRemove);   
			    } else {	 
				    if((!$isCatalogueAdded))  
				    {	 
				    	$this->addProductToCart($catalogueItem,$cart);   
			   		}
			    }  
			}
		}
	}
 
	
	public function addProductToCart($catalogueItem,$cart)
	{ 
			try { 
				$product  = Mage::getModel('catalog/product')
		              					  ->setStoreId(Mage::app()->getStore()->getId());
				$product->load($product->getIdBySku($catalogueItem)); 
		    	$params['qty'] = 1;
		    	$params['product'] = $product->getId();  
			    $quote = $this->_getQuote(); 
				$objCatalogueItem = $quote->addProduct($product,1); 
				$objCatalogueItem->setCatalogueItem(1);
				$objCatalogueItem->save(); 
				 
				Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('core')->__('Free catalogue is added to cart.'));
			}  catch (Exception $e)
			{
				
			}
	}
	
	
   /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }
	  /**
     * Retrieve order create model
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

}