<?php


class Krishinc_Sourcecode_Model_Type_Onepage extends TinyBrick_GiftCard_Model_Type_Onepage
{
    public function initCheckout()
    {
        $checkout = $this->getCheckout();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if (!($step==='login'
                    || Mage::getSingleton('customer/session')->isLoggedIn() && $step==='billing')) {
                    $checkout->setStepData($step, 'allow', false);
                }
            }
        }

        $checkout->setStepData('sourcecode', 'allow', true);

        /*
        * want to laod the correct customer information by assiging to address
        * instead of just loading from sales/quote_address
        */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }
        return $this;
    }
    
    /*********RELATED TO EM_NEWSLETTEROPITON MODULE*********/
     public function saveBilling($data, $customerAddressId)
    {
        if (isset($data['is_subscribed']) && !empty($data['is_subscribed'])){
            $this->getCheckout()->setCustomerIsSubscribed(1);
        }
        else {
            $this->getCheckout()->setCustomerIsSubscribed(0);
        }
  	
        if(isset($data['customer_type']) && !empty($data['customer_type']))
        { 
        	$this->getCheckout()->setCustomerType($data['customer_type']); 
        	Mage::getSingleton('core/session')->setCustomerType($data['customer_type']); 
        } 
        return parent::saveBilling($data, $customerAddressId);
    }
}
