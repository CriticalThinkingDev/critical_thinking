<?php

require_once 'Mage/Newsletter/controllers/SubscriberController.php';

class Krishinc_Overridenewsletter_Subscribercontroller extends Mage_Newsletter_SubscriberController 
{
	  
	public function customsubscribeAction()
	{  
		$this->loadLayout();
		$this->renderLayout();
	}

public function customsubscribe2Action()
	{  
		$this->loadLayout();
		$this->renderLayout();
	}

public function customsubscribe3Action()
	{  
		$this->loadLayout();
		$this->renderLayout();
	}
 public function customsubscribe_prekAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function customsubscribe_k_2Action()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function customsubscribe_3_5Action()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function customsubscribe_6_8Action()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	
	public function sidebarNewAction()
	{
		if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
			$postData =  $this->getRequest()->getParams();
			$session            = Mage::getSingleton('core/session');
			if(isset($postData) && isset($postData['resale']))
			{
				$url = Mage::getUrl('').'overridenewsletter/subscriber/customsubscribe?resale=1'; 
				$session->setRedirectFromResale(true);
			}else {
				$url = Mage::getUrl('').'overridenewsletter/subscriber/customsubscribe'; 
				$session->setRedirectFromResale(false);
			}
			$session->setFormData($postData); 
		
			$this->_redirectUrl($url);
		  	return; 
			
		}
	}
	
	   /**
      * New subscription action
      */
    public function saveAction()
    { 
    	
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');
            $email = trim($email);
			$postData			= $this->getRequest()->getParams();
$postData['email'] = $email;
			
            try {
            	$validate = $this->_isValideCaptcha();
$validate = 1;
    			if($validate)
	    		{
		                if (!Zend_Validate::is($email, 'EmailAddress')) {
		                    Mage::throwException($this->__('Please enter a valid email address.'));
		                } 
		
		                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
		                    !$customerSession->isLoggedIn()) {
		                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
		                }
		
		                $ownerId = Mage::getModel('customer/customer')
		                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
		                        ->loadByEmail($email)
		                        ->getId();
		                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
		                	//$session->setFormData($this->getRequest()->getParams());
		                   // Mage::throwException($this->__('This email address is already assigned to another user.'));
		                }
		  $emailList = $postData['email_list'];
                        $concatlist = '';
                        $totalCount = count($emailList);
                         $c = 0;
                        foreach($emailList as $list){
                            $c++;
                            if($c>1){
                                $concatlist .= ' & ';
                            }
                            if($list=='2405007'){
                                $concatlist .=' Grade PreK';
                            }
                            if($list=='2405008'){
                                $concatlist .='Grades K-2';
                            }
                            if($list=='2405009'){
                                $concatlist .='Grades 3-5';
                            }
                            if($list=='2405010'){
                                $concatlist .='Grades 6-8';
                            }
                            if($list=='2405405'){
                                $concatlist .='Bi-monthly Best Sale Alerts';
                            }

                        }

                        $model = Mage::getModel('offerlanding/puzzle');
                        $model->setData($postData);
                    $model->setEmaillist($concatlist);
                       $model->setBestDescribe($postData['position']);
                    if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                        $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                    } else {
                        $model->setUpdateTime(now());
                    }
                    $model->save();
		                $status = Mage::getModel('overridenewsletter/subscriber')->subscribe($postData); //changed parameter value
		                $subscriberData = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
		               
		                Mage::dispatchEvent('overridenewsletter_subscriber_save_after',
		                        array('subscriber' => Mage::getModel('newsletter/subscriber')->loadByEmail($email))
		                 );   
		                /**START: To subscribe for listrack  **/ 
				    	//Mage::getModel('overridenewsletter/subscriber')->subscribeToListrack($subscriberData);
						/****END****/   
		                
		                $session->unsFormData();  
		                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
		                    $session->addSuccess($this->__('Confirmation request has been sent.'));
		                }
		                else {
		                	if(isset($postData['position']) && $postData['position'] == 'Reseller') 
		                	{
		                		$session->setRedirectFromResale(true);
		                    	
		                	} else {
		                		$session->setRedirectFromResale(false);
		                	}
		                	$session->addSuccess($this->__('Thank you for your subscription.'));
		                }
 $url=Mage::getUrl('thankyou');


                    Mage::app()->getFrontController()->getResponse()->setRedirect($url);

                    return;
    			} else {
    				$url = Mage::getUrl('').'overridenewsletter/subscriber/customsubscribe'; 
    				if(isset($postData['position']) && $postData['position'] == 'Reseller') 
                	{
                		$url .='?resale=1';
                		$session->setRedirectFromResale(true); 
                	} else {
                		$session->setRedirectFromResale(false);
                	}
    				$session->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.')); 
                	$session->setFormData($this->getRequest()->getParams());
                	
					$this->_redirectUrl($url);
				  	return; 
//    				$this->_redirect('*/*');
//				  	return;
    			}
            }
            catch (Mage_Core_Exception $e) {
            	if(isset($postData['position']) && $postData['position'] == 'Reseller') 
            	{
            		$session->setRedirectFromResale(true); 
            	} else {
            		$session->setRedirectFromResale(false);
            	} 
            	$session->setFormData($this->getRequest()->getParams());
                $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
            }
            catch (Exception $e) {
            	if(isset($postData['position']) && $postData['position'] == 'Reseller') 
            	{
            		$session->setRedirectFromResale(true);                	
            	} else {
            		$session->setRedirectFromResale(false);
            	}
            	$session->setFormData($this->getRequest()->getParams());
                $session->addException($e, $this->__('There was a problem with the subscription.'));
            }
        }
        $this->_redirectReferer();
    }
      
    public function _getSession()
    {
    	return  Mage::getSingleton('core/session');
    }
    
    public function _isValideCaptcha()
    {
    	$formId = 'custom_newsletter';
    	
    	$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
     
    	$captchaParams = $this->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
          
    	  if (!$captchaModel->isCorrect($captchaParams[$formId])) { 
                return false;
            } else {
            	return true;
            }
    }
   
}
