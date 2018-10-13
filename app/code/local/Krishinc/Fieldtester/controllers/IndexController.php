<?php
class Krishinc_Fieldtester_IndexController extends Mage_Core_Controller_Front_Action
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
    		try {
    			
    			$validate = $this->_isValideCaptcha();
		$validate = 1;
    			if($validate)
    			{ 
    			
	    			$model = Mage::getModel('fieldtester/fieldtester');
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
			    	 Mage::dispatchEvent('fieldtester_index_createpost_after', array('requestdata' => $model));  
				/****END****/   
							
					/**START: To subscribe for mail chimp**/
//					$listId = '8f169da12b';
//					$address = $model->getAddress1() .','. $model->getAddress2();
//					$model->setAddress($address);
//	 				Mage::getModel('monkey/monkey')->insertIntoMailChimp($model,$listId);  
	 				/**END**/
	    			$this->_getSession()->unsFtFormData();  	    			
	    			$this->_getSession()->addSuccess('Your inquiry was submitted and will be responded to as soon as possible.');
    			
					$this->_redirect('*/*');
				  	return;
    			} else {
    				$this->_getSession()->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.')); 
                	$this->_getSession()->setFtFormData($this->getRequest()->getParams());  
    				$this->_redirect('*/*');
				  	return;
    			}
    		} catch (Mage_Core_Exception $e) {
                $this->_getSession()->setFtFormData($this->getRequest()->getParams())
                    ->addException($e, $e->getMessage());
                $this->_redirect('*/*');
                return;
            } catch (Exception $e) {
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
    	$formId = 'user_fieldtester';
    	
    	$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
     
    	$captchaParams = $this->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
          
    	  if (!$captchaModel->isCorrect($captchaParams[$formId])) { 
                return false;
            } else {
            	return true;
            }
    }
   
}
