<?php
class Krishinc_Overridepo_Model_Adminhtml_Observer extends Krishinc_Sourcecode_Model_Observer
{
	public function adminhtml_sales_order_create_process_data(Varien_Event_Observer $observer)
	{ 
		try {
            $requestData = $observer->getEvent()->getRequest();
            
            if (isset($requestData['order']['order_type'])) {
                $observer->getEvent()->getOrderCreateModel()->getQuote()
                    ->addData($requestData['order']) 
                    ->save();
            } 
            /**
             * Added code to store is_taxexempt field value to order
             */
           if ($customerGroupId = $observer->getEvent()->getOrderCreateModel()->getQuote()->getCustomerGroupId()) 			{
	        	if($customerGroupId == 4)
	        	{
	        		$requestData['order']['is_taxexempt'] = 1;  
	        	} else{
	        	    $requestData['order']['is_taxexempt'] = 0;
	        	}
	        	 $observer->getEvent()->getOrderCreateModel()->getQuote()
                    ->addData($requestData['order']) 
                    ->save();
	        } 
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
	}
	
	
    /**
     * If the quote has a delivery note then lets save that note and
     * assign the id to the order
     *
     * @param Varien_Event_Observer $observer
     * @return Krishinc_Sourcecode_Model_Observer
     */
    public function sales_convert_quote_to_order(Varien_Event_Observer $observer)
    {
        if ($ordertype = $observer->getEvent()->getQuote()->getOrderType()) {
            try {  
                $observer->getEvent()->getOrder()
                    ->setOrderType($ordertype);

            } catch (Exception $e) {
                Mage::logException($e);
            }
        }       
        /**
         * Added code to store is_taxexempt field value to order
         */
        if ($customerGroupId = $observer->getEvent()->getQuote()->getCustomerGroupId()) {
        	if($customerGroupId == 4)
        	{
        		   $observer->getEvent()->getOrder()
                    ->setIsTaxexempt(1);

        	} else{
        		   $observer->getEvent()->getOrder()
                    ->setIsTaxexempt(0);
        	}
        }
        return $this;
    }
}