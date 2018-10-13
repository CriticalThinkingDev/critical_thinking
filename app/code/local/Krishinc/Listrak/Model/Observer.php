<?php
class Krishinc_Listrak_Model_Observer
{
	public function catalogrequestSaveAfter(Varien_Event_Observer $observer)
	{
		if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		
		if(!Mage::helper('listrak')->canCatalogrequest()){
			return $observer;
		} 
		 
		if($data = $observer->getEvent()->getRequestdata())
		{  
			Mage::helper('listrak')->subscribeToListrack($data,'catalogrequest'); 
		} 
	}	
	
	public function offerlandingSaveAfter(Varien_Event_Observer $observer)
	{
		if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		
		if(!Mage::helper('listrak')->canOfferlanding()){
			return $observer;
		} 
		 
		if($data = $observer->getEvent()->getRequestdata())
		{ 
			Mage::helper('listrak')->subscribeToListrack($data,'offerlanding'); 
		} 
	}

  public function ebookSaveAfter(Varien_Event_Observer $observer)
    {
        if(!Mage::helper('listrak')->canListrak()){
            return $observer;
        }

        if(!Mage::helper('listrak')->canOfferlanding()){
            return $observer;
        }

        if($data = $observer->getEvent()->getRequestdata())
        {
            Mage::helper('listrak')->subscribeToListrackebook($data,'offerlanding');
        }
    }
	public function contactusSaveAfter(Varien_Event_Observer $observer)
	{
		if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		
		if(!Mage::helper('listrak')->canContactus()){
			return $observer;
		} 
		 
		if($data = $observer->getEvent()->getRequestdata())
		{ 
			Mage::helper('listrak')->subscribeToListrack($data,'contactus');  
		} 
	}
	
	public function newsletterSaveAfter(Varien_Event_Observer $observer)
	{
		 if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		
		if(!Mage::helper('listrak')->canNewsletter()){
			return $observer;
		} 
		
		if($data = $observer->getEvent()->getSubscriber())
		{    $supplier = Mage::app()->getRequest()->getParam('supply');
            		if($supplier){
                $data->setSupply($supplier);
            }
			Mage::helper('listrak')->subscribeToListrack($data,'overridenewsletter'); 
		}
	}	
	
	
	public function fieldtesterSaveAfter(Varien_Event_Observer $observer)
	{
		 if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		
		if(!Mage::helper('listrak')->canFieldtester()){
			return $observer;
		} 
		
		if($data = $observer->getEvent()->getRequestdata())
		{   
			Mage::helper('listrak')->subscribeToListrack($data,'fieldtester'); 
		}
	}

	
	public function dealerskitSaveAfter(Varien_Event_Observer $observer)
	{
		if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		
		if(!Mage::helper('listrak')->canDealerskit()){
			return $observer;
		} 
		 
		if($data = $observer->getEvent()->getRequestdata())
		{  
			Mage::helper('listrak')->subscribeToListrack($data,'dealerskit'); 
		} 
	}	
	/**
	 * Function to send newsletter data to listrak
	 *
	 * @param Varien_Event_Observer $observer
	 * 
	 */
	public function adminNewsletterSaveAfter(Varien_Event_Observer $observer)
	{
		
		if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		if(!Mage::helper('listrak')->canNewsletter()){
			return $observer;
		}   
		 
		$subscription = $observer->getEvent()->getRequest()->getParams();
		 
		if(isset($subscription['subscription']) or isset($subscription['account']['subscription']))
		{
			$data = '';
			$customer = $observer->getEvent()->getCustomer(); 
			$data = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail()); 
			Mage::helper('listrak')->subscribeToListrack($data,'overridenewsletter');  
		}
	}

	public function frontendNewsletterSaveAfter(Varien_Event_Observer $observer)
	{

		if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		if(!Mage::helper('listrak')->canNewsletter()){
			return $observer;
		}
		$subscription = Mage::app()->getRequest()->getParams();
		if($subscription['is_subscribed'])
		{
			$data =  Mage::app()->getRequest()->getParams();

			Mage::helper('listrak')->subscribeToListrackCustomer($data,'overridenewsletter');
		}
	}
        //checkout_submit_all_after event
	public function checksubmitAfterAdmin(Varien_Event_Observer $observer)
	{
		
		if(!Mage::helper('listrak')->canListrak()){
			return $observer;
		}
		if(!Mage::helper('listrak')->canNewsletter()){
			return $observer;
		}   
$isEmailList = Mage::getSingleton('checkout/session')->getIsjoinemaillist();
 Mage::getSingleton('checkout/session')->setjoinemaillistnew($isEmailList);
                Mage::getSingleton('checkout/session')->unsIsjoinemaillist();
                if(!$isEmailList){

                   Mage::log('not sync',null,'pslogggg.log');
			return $observer;
                }else{
                                      Mage::log('sync',null,'pslogggg.log');
                 }
		
		$subscription = $observer->getEvent()->getQuote()->getIsSubscribed();
		
		if(isset($subscription) && $subscription==1)
		{ 
			$data = array();
			$customer = $observer->getEvent()->getQuote()->getCustomer(); 

			$data = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail()); 
			Mage::helper('listrak')->subscribeToListrack($data,'overridenewsletter');  
		}

	}
}
