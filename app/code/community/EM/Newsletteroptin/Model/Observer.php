<?php
class EM_Newsletteroptin_Model_Observer
{
    public function setCustomerIsSubscribed($observer)
    {
        if ((bool) Mage::getSingleton('checkout/session')->getCustomerIsSubscribed()){
            $quote = $observer->getEvent()->getQuote();
            $customer = $quote->getCustomer();
            switch ($quote->getCheckoutMethod()){
                case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER:
                    $customer->setIsSubscribed(1); 
                    $customer->setCustomerType(Mage::getSingleton('checkout/session')->getCustomerType()); 
                    break;
				case Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN:
					$customer->setIsSubscribed(1); 
					break;
                case Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST:
                    $session = Mage::getSingleton('core/session');
                    $customer->setCustomerType($session->getCustomerType());
                    try {
                    	$data = array(); 
                    	$allCustomerType = Mage::getModel('advancecustomer/source_marketarray')->getKeyValueOptions();
                    	$data['position'] = $allCustomerType[$customer->getCustomerType()];   
                    	$data['firstname']= $quote->getBillingAddress()->getFirstname();
                    	$data['lastname']= $quote->getBillingAddress()->getLastname();
                    	$data['email']= $quote->getBillingAddress()->getEmail();
                     	$session->unsCustomerType(); 
                       // $status = Mage::getModel('newsletter/subscriber')->subscribe($quote->getBillingAddress()->getEmail());
                        $status = Mage::getModel('overridenewsletter/subscriber')->subscribe($data);  
                        /*******START:: Added to insert in listrak*****/
                        $subscriberData = Mage::getModel('newsletter/subscriber')->loadByEmail($quote->getBillingAddress()->getEmail());
                        Mage::helper('listrak')->subscribeToListrack($subscriberData,'overridenewsletter'); //Added to insert subscribe newsletter email to listrak    
                        /******END*******/
                        if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE){
                            $session->addSuccess(Mage::helper('newsletteroptin')->__('Confirmation request has been sent regarding your newsletter subscription'));
                        }
                    } 
                    catch (Mage_Core_Exception $e) {
                        $session->addException($e, Mage::helper('newsletteroptin')->__('There was a problem with the newsletter subscription: %s', $e->getMessage()));
                    }
                    catch (Exception $e) {
                        $session->addException($e, Mage::helper('newsletteroptin')->__('There was a problem with the newsletter subscription'));
                    }
                    break;
            }
            Mage::getSingleton('checkout/session')->setCustomerIsSubscribed(0);
        }
    }
}