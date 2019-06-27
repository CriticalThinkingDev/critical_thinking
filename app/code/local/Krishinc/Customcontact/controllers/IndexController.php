<?php
class Krishinc_Customcontact_IndexController extends Mage_Core_Controller_Front_Action
{
	
    const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    const XML_PATH_ENABLED          = 'contacts/contacts/enabled';

    public function indexAction()
    {  
		$this->loadLayout();     
		$this->renderLayout();
    }
    
 
    public function createpostAction()
    {
    	if($data = $this->getRequest()->getPost())
    	{ 
    		$translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
    		try {
    			
    			//$validate = $this->_isValideCaptcha();
			//$validate = 1;
$response = Mage::helper('studioforty9_recaptcha/request')->verify();
$validate =  $response->isSuccess(); 
    			if($validate==1)
    			{ 
	    			$model = Mage::getModel('customcontact/customcontact');
	    			$model->setData($data)
	    				  ->setStatus('2');
	    			
					if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
						$model->setCreatedTime(now())
							->setUpdateTime(now());
					} else {
						$model->setUpdateTime(now());
					}	  
				   $model->save(); 
				   $subjects = Mage::helper('customcontact')->getAllSubjects();
				   $data['subjects'] = $subjects[$data['subject']];
				   $foundvia = Mage::helper('customcontact')->getAllFoundvia();
				   if(isset($data['used_products'])) {
                    $data['used_products'] = ucfirst($data['used_products']);     
                   }
                   if(isset($data['foundvia'])) {
				    $data['foundvia'] = (isset($foundvia[$data['foundvia']])?$foundvia[$data['foundvia']]:'');
				   }
				   $data['catalogvalue'] =  Mage::getStoreConfig('customer/catalogrequest/catalog_code'); 
				    
				   $postObject = new Varien_Object(); 
                   $postObject->setData($data);
				   
				   $mailTemplate = Mage::getModel('core/email_template');
	                /* @var $mailTemplate Mage_Core_Model_Email_Template */
	                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
	                    ->setReplyTo($data['email'])
	                    ->sendTransactional(
	                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
	                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                                
	                        $this->getSubjectWiseReciepentEmail($data['subject']),
	                        null,
	                        array('data' => $postObject)
	                    );
	 
	                if (!$mailTemplate->getSentSuccess()) {
	                    throw new Exception();
	                }
	
	                $translate->setTranslateInline(true);
					/**START: To subscribe for listrack  **/  
			    	// Mage::dispatchEvent('contactus_index_createpost_after', array('requestdata' => $model));  
					/****END****/   	   
 
	    			$this->_getSession()->unsCcFormData();  	    			
	    			$this->_getSession()->addSuccess('Your inquiry was submitted and will be responded to as soon as possible.');
    			
					$this->_redirect('*/*');
				  	return;
    			} else {
    				$this->_getSession()->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.')); 
                	$this->_getSession()->setCcFormData($this->getRequest()->getParams());  
    				$this->_redirect('*/*');
				  	return;
    			}
    		} catch (Mage_Core_Exception $e) {
    			 $translate->setTranslateInline(true);
                $this->_getSession()->setCcFormData($this->getRequest()->getParams())
                    ->addException($e, $e->getMessage());
                $this->_redirect('*/*');
                return;
            } catch (Exception $e) {
            	$translate->setTranslateInline(true);
                $this->_getSession()->setFtFormData($this->getRequest()->getParams())
                    ->addException($e, $this->__('Cannot save your Offer.'));
                 $this->_redirect('*/*');
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
    	$formId = 'user_customcontact';
    	
    	$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
     
    	$captchaParams = $this->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
          
    	  if (!$captchaModel->isCorrect($captchaParams[$formId])) { 
                return false;
            } else {
            	return true;
            }
    }
    
    public function getSubjectWiseReciepentEmail($subject)
    {
    	if (trim($subject) == "OurWebsite") { 
                $email = "marketing@criticalthinking.com";  
		} 
		else if (trim($subject) == "BookProducts") { 
                $email = "books@criticalthinking.com"; 
		} 
		else if (trim($subject) == "SoftwareProducts") { 
                $email = "software@criticalthinking.com"; 
		} 
		else if (trim($subject) == "ResellerQuestions") { 
                $email = "dealers@criticalthinking.com"; 
                 
		} 
		else if (trim($subject) == "NewProductIdeas") { 
                $email = "ideas@criticalthinking.com";
		}  
		else if (trim($subject) == "YourOrder") { 
                $email = "service@criticalthinking.com"; 
		} 
		else if (trim($subject) == "Permissions") { 
                $email = "marketing@criticalthinking.com"; 
		} 
		else if (trim($subject) == "SuccessStory") { 
                $email = "marketing@criticalthinking.com"; 
		} 
		else if (trim($subject) == "Remove") { 
                $email = "service@criticalthinking.com"; 
		} 
		else if (trim($subject) == "Unsubscribe") { 
                $email = "webmaster@criticalthinking.com"; 
		} 
		else if (trim($subject) == "Quote") { 
                $email = "sales@criticalthinking.com"; 
		} else { 
                $email = "info@criticalthinking.com"; 
		}
		return $email; 
    }
   
}
