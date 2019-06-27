<?php

//require_once 'Mage/Checkout/controllers/OnepageController.php';
require_once 'IWD/AddressVerification/controllers/OnepageController.php';

//class Krishinc_Sourcecode_OnepageController extends Mage_Checkout_OnepageController
class Krishinc_Sourcecode_OnepageController extends IWD_AddressVerification_OnepageController
{
    public function doSomestuffAction()
    {
		if(true) {
			$result['update_section'] = array(
            	'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
			);					
		}
    	else {
			$result['goto_section'] = 'shipping';
		}		
    }    
 
 

    public function savePaymentAction()
    {
        $this->_expireAjax();
if ($this->isFormkeyValidationOnCheckoutEnabled() && !$this->_validateFormKey()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
           
            
            try {
                $result = $this->getOnepage()->savePayment($data);
            }
            catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            }
            catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
				$this->loadLayout('checkout_onepage_sourcecode');

                $result['goto_section'] = 'sourcecode';
            }

            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    public function saveSourcecodeAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            
        	//Grab the submited value Sourcecode value
        	$_Sourcecode = $this->getRequest()->getPost('source_code');
			Mage::getSingleton('core/session')->setKrishincSourcecode($_Sourcecode);

			$result = array();
            
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (!$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');

                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );

            }

            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }  
     public function successAction()
    {
        if (!$this->getOnepage()->getCheckout()->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        Mage::getSingleton('checkout/session')->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action',array('order_id'=>$lastOrderId, 'quote_id' => $lastQuoteId));
        $this->renderLayout();
    }
      
}
