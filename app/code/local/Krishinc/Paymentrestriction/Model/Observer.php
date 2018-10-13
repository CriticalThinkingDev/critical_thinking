<?php

class Krishinc_Paymentrestriction_Model_Observer
{
    public function paymentMethodIsActive($observer)
    {
        $instance = $observer->getMethodInstance();
        $result = $observer->getResult();
		$allowedPaymentMethods = Mage::helper('paymentrestriction')->getAllowedAdminPaymentMethods();  
        if (in_array($instance->getCode(),$allowedPaymentMethods)) {
            if (Mage::app()->getStore()->isAdmin()) {
                $result->isAvailable = true;
            } else {
                $result->isAvailable = false;
            }
        }
    }
}