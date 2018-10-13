<?php
class Krishinc_Offerlanding_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {  
		$this->loadLayout();     
		$this->renderLayout();
    }
    
 
    public function createpostAction()
    {
    	if($data = $this->getRequest()->getPost())
    	{
    		  $email              = trim($this->getRequest()->getPost('email'));
            $data['email'] = $email;
    		try {
    			
    			$validate = $this->_isValideCaptcha();
			$validate  = 1;
    			if($validate)
    			{
    			
	    			$model = Mage::getModel('offerlanding/offerlanding');
//	    			if(!$model->isEmailExists($data['email']))
//	    			{
			    			$model->setData($data)
			    				  ->setStatus('2');
			    			
							if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
								$model->setCreatedTime(now())
									->setUpdateTime(now());
							} else {
								$model->setUpdateTime(now());
							}
							 
							
							$model->save();	 
							
							/**START: To subscribe for listrack  **/ 
						    	//Mage::getModel('offerlanding/offerlanding')->subscribeToListrack($model);
						    	 Mage::dispatchEvent('offerlanding_index_createpost_after', array('requestdata' => $model));  
							/****END****/   
							   
							$this->_getSession()->setIsRedirectToDownload($model->getId());
			    			$this->_getSession()->unsOlFormData();  	    			
			    			$this->_getSession()->addSuccess('Thank you for signing up for our catalog and monthly newsletter! Download your free gifts below.');
			    			
			    			$url = Mage::getUrl('').'free-gift-download'; 
							$this->_redirectUrl($url);
						  	return;
//						  	} else {
//			    				$this->_getSession()->addError(Mage::helper('captcha')->__('Email Address Already Exists!')); 	                	
//			    				$this->_getSession()->setOlFormData($this->getRequest()->getParams());  
//			    				$this->_redirect('*/*');
//							  	return;
//			    			}
	    			} else {
	    				$this->_getSession()->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.')); 
	                	$this->_getSession()->setOlFormData($this->getRequest()->getParams());  
	    				$url = Mage::getUrl('').'free-gift-form'; 
						$this->_redirectUrl($url);
					  	return;
	    			}
    		} catch (Mage_Core_Exception $e) {
                $this->_getSession()->setOlFormData($this->getRequest()->getParams())
                    ->addException($e, $e->getMessage());
                $url = Mage::getUrl('').'free-gift-form'; 
				$this->_redirectUrl($url);
                return;
            } catch (Exception $e) {
                $this->_getSession()->setOlFormData($this->getRequest()->getParams())
                    ->addException($e, $this->__('Cannot save your Offer.'));
                $url = Mage::getUrl('').'free-gift-form'; 
				$this->_redirectUrl($url);
                 return;
            }
            
    	} else {	 
			$this->_redirect('*/*');
    	}
    }
    
    public function _getSession()
    {
    	return  Mage::getSingleton('core/session');
    }
    
    public function _isValideCaptcha()
    {
    	$formId = 'user_offerlanding';
    	
    	$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
     
    	$captchaParams = $this->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
          
    	  if (!$captchaModel->isCorrect($captchaParams[$formId])) { 
                return false;
            } else {
            	return true;
            }
    }
   
}
