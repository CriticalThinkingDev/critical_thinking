<?php

class Krishinc_Free_Helper_Data extends Mage_Core_Helper_Abstract
{
//	public function validateOrder($data)
//	{
//		/** @var $order Mage_Sales_Model_Order */
//        $order = Mage::getModel('sales/order');
//        $order->loadByIncrementId($data['order_number']);
//        $errors = false;
//         if ($order->getId()) {
//                $billingAddress = $order->getBillingAddress();
//                if ((strtolower($data['name']) != strtolower($billingAddress->getFirstname().' '. $billingAddress->getLastname()))) {
//                    $errors = true;
//                }
//            } else {
//                $errors = true;
//            }
//		if($errors)
//		{
//			return false;
//		} else {
//			return true;
//		}
//	}

 
	public function validateOrder($data)
	{
		/** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order');
        $orderID = trim($data['order_number']);
        $order->loadByIncrementId($orderID);

        $errors = array();
        $errors['error'] = false;
         if ($order->getId()) {
                $billingAddress = $order->getBillingAddress();
                $nameArr = explode(' ',$data['name']);
                $str = '';
                foreach ($nameArr as $name)
                {
                	 $name = trim($name);
                	 if($name != '') { 
                		$str .= $name.' ';
                	 }
                } 
             	$name = trim($str); 
                if ((strtolower($name) != strtolower(trim($billingAddress->getFirstname()).' '. trim($billingAddress->getLastname())))) {
                    $errors ['error']= true;
                    $errors ['message']= $this->__('Invalid Full Name.');
                } 
//                if($order->getCustomerIsGuest())
//                {
//                	$errors ['error']= true;
//                    $errors ['message']= $this->__('Only registered user can able to download free gift!');
//                }
                
            } else {
                $errors ['error']= true;
                $errors ['message']= $this->__('Invalid Name/Order No.');
            }
		return $errors;
	}
}