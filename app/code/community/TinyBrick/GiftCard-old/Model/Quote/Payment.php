<?php

class TinyBrick_GiftCard_Model_Quote_Payment extends Mage_Sales_Model_Quote_Payment
{
	public function importData(array $data)
    {
    	//find out if the customer is using a giftcard
		$cards = Mage::getModel('giftcard/payment')->getCollection()
			->addFieldToFilter('quote_id', $this->getQuote()->getId());
		if(count($cards)) {
			if($this->getQuote()->getGrandTotal() == 0) {
				$data = array('method'=>'free');
			}
		}
		
        $data = new Varien_Object($data);
   
        Mage::dispatchEvent(
            $this->_eventPrefix . '_import_data_before',
            array(
                $this->_eventObject=>$this,
                'input'=>$data,
            )
        );

        $this->setMethod($data->getMethod());
        
        $method = $this->getMethodInstance();

		$this->save();
        /**
         * Payment avalability related with quote totals.
         * We have recollect quote totals before checking
         */
        $this->getQuote()->collectTotals();
        
		if(!$this->getQuote()->getGcCoveredAll()) {
	        if (!$method->isAvailable($this->getQuote())) {
	            Mage::throwException(Mage::helper('sales')->__('The requested Payment Method is not available.'));
	        }
        }

        $method->assignData($data);

        /*
        * validating the payment data
        */
        $method->validate();
        
        if(!$this->getCcNumberEnc()) {
        	$info = $method->getInfoInstance();
        	$this->setCcNumberEnc($info->encrypt($info->getCcNumber()));

        	$this->save();
        }
        return $this;
    }
    
}