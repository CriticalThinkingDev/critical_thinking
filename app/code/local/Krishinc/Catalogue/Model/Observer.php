<?php

class Krishinc_Catalogue_Model_Observer
{
	public function checkoutAddCatalogueProduct(Varien_Event_Observer $observer)
	{
		$helper = Mage::helper('catalogue');
		if(!$helper->config('enabled'))
		{
			return;			 
		}
		if($catalogueItem = $helper->getCatalogueProductFromCurrentMonth())
		{	
	 
			$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$catalogueItem);
	    
	    	$quote = $this->_getQuote(); 
 			$cart = Mage::getSingleton('checkout/cart');
			$totalQuoteItem = count($quote->getAllVisibleItems()); 
			if($totalQuoteItem > 0) {
				
			    $isCatalogueAdded = false;
			    foreach($quote->getAllVisibleItems() as $item) { 
			    	if($item->getCatalogueItem())
			    	{ 
			    		if($item->getSku() != $catalogueItem)
			    		{
			    			$cart->removeItem($item->getId());  
			    			 
			    		}
			    	}
			    	if($item->getSku() == $catalogueItem)
			    	{
			    		$isCatalogueAdded  = true;
			    		$item->setQty(1);
			    		$item->setCatalogueItem(1);  
			    		$item->save();
			    		 
			    	}  
			    }
		        if(($totalQuoteItem == 1) && ($isCatalogueAdded == true))
			    {
			    	$cart->truncate();  
			    	 
			    } else {	 
				     if(!$isCatalogueAdded)
				    {	
				    	$this->addProductToCart($catalogueItem,$cart);   
				     
			   		 }
			    } 
			}
		}
	}
	
/*	
	public function checkoutCartaddProductAfter(Varien_Event_Observer $observer)
	{
		$helper = Mage::helper('catalogue');
		if(!$helper->config('enabled'))
		{
			return;			 
		}
		if($catalogueItem = $helper->getCatalogueProductFromCurrentMonth())
		{	
	 
			$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$catalogueItem);
	    
	    	$quote = $this->_getQuote(); 
 			$cart = Mage::getSingleton('checkout/cart');
			if($quote->getAllVisibleItems()) {
				
			    $isCatalogueAdded = false;
			    foreach($quote->getAllVisibleItems() as $item) { 
			    	if($item->getCatalogueItem())
			    	{
			    		if($item->getSku() != $catalogueItem)
			    		{
			    			$cart->removeItem($item->getItemId()); 
			    		}
			    	}
			    	if($item->getSku() == $catalogueItem)
			    	{
			    		$isCatalogueAdded  = true;
			    	}
			    }
			    if(!$isCatalogueAdded)
			    {	
			    	$this->addProductToCart($catalogueItem,$cart);  
			    } 
			}
		}
	}
	
	public function checkoutCartUpdateItemsAfter(Varien_Event_Observer $observer)
	{	
		$helper = Mage::helper('catalogue');
		if(!$helper->config('enabled'))
		{
			return;			  
		}
		if($catalogueItem = $helper->getCatalogueProductFromCurrentMonth())
		{  
			$quote =  Mage::getSingleton('checkout/session')->getQuote();
			$cart = Mage::getSingleton('checkout/cart');
			$totalQuoteItem = count($quote->getAllVisibleItems()); 
			if($totalQuoteItem > 0) {
				
			    $isCatalogueAdded = false;
			    foreach($quote->getAllVisibleItems() as $item) {
			    	if($item->getSku() == $catalogueItem)
			    	{
			    		$isCatalogueAdded  = true;
			    	} 
			    } 
			    if(($totalQuoteItem == 1) && ($isCatalogueAdded == true))
			    {
			    	$cart->truncate(); 
			    } else {	 
				    if(!$isCatalogueAdded)
				    { 
				    	$this->addProductToCart($catalogueItem,$cart);
				    } 
			    } 
			}
		}
		
	}
	
	
	public function salesQuoteRemoveItem(Varien_Event_Observer $observer)
	{	
		$helper = Mage::helper('catalogue');
		if(!$helper->config('enabled'))
		{
			return;			  
		} 
		if($catalogueItem = $helper->getCatalogueProductFromCurrentMonth())
		{  
			$quote =  Mage::getSingleton('checkout/session')->getQuote();
			$cart = Mage::getSingleton('checkout/cart');
			$totalQuoteItem = count($quote->getAllVisibleItems()); 
			if($totalQuoteItem > 0) {
				
			    $isCatalogueAdded = false;
			    foreach($quote->getAllVisibleItems() as $item) {
			    	if($item->getSku() == $catalogueItem)
			    	{
			    		$isCatalogueAdded  = true;
			    	} 
			    }
			    if(($totalQuoteItem == 1) && ($isCatalogueAdded == true))
			    {
			    	$cart->truncate();
			    } else {
				    if(!$isCatalogueAdded)
				    {
				    	$this->addProductToCart($catalogueItem,$cart);
				    } 
			    }
			}
		} 
	}*/  
	
	public function addProductToCart($catalogueItem,$cart)
	{ 
			try {
				$product  = Mage::getModel('catalog/product')
		              					  ->setStoreId(Mage::app()->getStore()->getId());
				$product->load($product->getIdBySku($catalogueItem)); 
		    	$params['qty'] = 1;
		    	$params['product'] = $product->getId(); 
//				$cart->addProduct($product, $params);  
//				$cart->save();  
			    $quote = $this->_getQuote(); 
				$objCatalogueItem = $quote->addProduct($product);
				$objCatalogueItem->setCatalogueItem(1);
				$objCatalogueItem->save(); 
				 
			//	Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('core')->__('Free catalogue is added to cart.'));
			}  catch (Exception $e)
			{
				
			}
	}
	
	
	protected function _getQuote()
	{
		if(!Mage::getSingleton('checkout/session')->getQuoteId()) {
			$quote = Mage::getModel('sales/quote')
    			->setId(null)
    			->setStoreId(1)
    			->setCustomerId('NULL')
    			->setCustomerTaxClassId(1);
    		$quote->save();
			Mage::getSingleton('checkout/session')->setQuoteId($quote->getId());
		} else {
			$quote = Mage::getSingleton('checkout/session')->getQuote();
		}
		return $quote;
	}
	
}