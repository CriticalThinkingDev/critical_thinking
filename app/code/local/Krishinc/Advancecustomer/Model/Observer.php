<?php

class Krishinc_Advancecustomer_Model_Observer 
{
   /**
     * Added code to store is_taxexempt field value to order
     */ 
	public function prepareCustomerSave(Varien_Event_Observer $observer)
	{
		$data = $observer->getEvent()->getRequest()->getParam('account');
		$customer = $observer->getEvent()->getCustomer(); 


		if(isset($data['subscription']))
		$customer->setIsSubscribed(1);		
//		
//		else
//		$customer->setIsSubscribed(0);	


	}
	
}
