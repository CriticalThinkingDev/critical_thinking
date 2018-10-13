<?php

class Krishinc_Overridepo_Model_Observer 
{
   /**
     * Added code to store is_taxexempt field value to order
     */ 
	public function sales_order_create_process_data(Varien_Event_Observer $observer)
	{ 
		try {
            $quoteData = $observer->getEvent()->getQuote();
            $orderData = $observer->getEvent()->getOrder();
             
           if ($customerGroupId = $quoteData->getCustomerGroupId()) 			
           {
	        	if($customerGroupId == 4)
	        	{
	        		 $quoteData->setIsTaxexempt(1)->save();  
	        	} else{
	        		 $quoteData->setIsTaxexempt(0)->save();  	        	     
	        	} 
	        } 
           if ($customerGroupId = $orderData->getCustomerGroupId()) 			
           {
	        	if($customerGroupId == 4)
	        	{
	        		 $orderData->setIsTaxexempt(1)->save();  
	        	} else{
	        		 $orderData->setIsTaxexempt(0)->save();    	        	     
	        	} 
	        } 
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
	}
	
}