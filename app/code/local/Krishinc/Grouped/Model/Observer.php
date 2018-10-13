<?php
 
class Krishinc_Grouped_Model_Observer
{
    public function addSeriesProduct($observer){
	
		$items  =  $observer->getProduct();
		// get the current Magento cart
		$cart = Mage::getModel('checkout/cart');
		$cart->init();
		$pModel = Mage::getSingleton('catalog/product');
		$i=0;
		$flag=0;
		foreach($items as $key => $value)
		{
		 if($value > 0)
		 {
		 	$flag=1;
			$pModel= $this->customInitProduct($key);
			$pModel->load($key);
			$cart->addProduct($pModel, array('qty' => $value));
		 }
		}
		$cart->save(); 		
		if($flag == 1)
		{
			 return 1;
		}else
		{
			return 0;
		}
  	 
	}
	
	protected function customInitProduct($productId)
    {
        //$productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
		
    //update a backend_type to 
    public function onAttributeSaveAfter(Varien_Event_Observer $observer)
	{   
	    $attribute = $observer->getEvent()->getAttribute();         
	    if (($attribute = $observer->getEvent()->getAttribute())
	        && ($attribute->getAttributeCode() == 'playroom_priority' || $attribute->getAttributeCode() == 'priority')) {   
	                       
	        $attribute->setData('backend_type','int');                   
	    }
	}
}