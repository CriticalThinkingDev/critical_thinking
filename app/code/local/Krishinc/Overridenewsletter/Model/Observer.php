<?php
class Krishinc_Overridenewsletter_Model_Observer extends Mage_Newsletter_Model_Observer
{
	public function subscribeCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof Mage_Customer_Model_Customer)) {
            Mage::getModel('overridenewsletter/subscriber')->subscribeCustomer($customer);
        }
        return $this;
    } 

}