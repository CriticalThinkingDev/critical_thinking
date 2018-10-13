<?php
class Krishinc_Catalogrequest_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    { 
 
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function preDispatch()
    {
        parent::preDispatch();

//        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
//            $this->setFlag('', 'no-dispatch', true);
//        }
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
    				 
	    			    $model = Mage::getModel('catalogrequest/catalogrequest');

	    			 
//	    				$model->setCatalogrequestId($model->isEmailExists($data['email']));
	    			 
		    			$model->setData($data)
		    				  ->setStatus('2');
		    			
						if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
							$model->setCreatedTime(now())
								->setUpdateTime(now());
						} else { 
							$model->setUpdateTime(now());
						}	 
						
						$model->save();	
						/**START: To subscribe for mail chimp**/
	//					If customer check the �Sign-up for our newsletter� checkbox then add them into Listrak. 
						if(isset($data['sign_up']) && ($data['sign_up'] == 1))
						{
								 Mage::dispatchEvent('catalogrequest_index_createpost_after', array('requestdata' => $model));  // Dispatch event to add entry in listrak email champaign system
							/***Add Entry in newsletter subscriber if email not exists***/
							$subscriber = Mage::getModel('overridenewsletter/subscriber');
				
							if($subscribeData = $subscriber->loadByEmail($model->getEmail())) 
							{    
								if(!$subscribeData->getId())
								{ 
									$subscribeData['firstname'] = $model->getFirstname();
									$subscribeData['lastname'] = $model->getLastname();
									$subscribeData['position'] = $model->getMarket();
									$subscribeData['email'] = $model->getEmail(); 
									$subscriber->subscribe($subscribeData); 
								}
							}
							/****END***/ 
					    
						}   
						 
						/****END****/   
						
		    			$this->_getSession()->unsCrFormData(''); 

//                        if($model->isEmailExists($data['email'])) {
                            $url = Mage::getUrl('').'catalogrequest-success';
                            $this->_redirectUrl($url);
//                        }else {
//                            $this->_getSession()->addSuccess('Your inquiry was submitted and a catalog will be mailed to you as soon as possible.');
//                            $this->_redirect('*/*');
//                            return;
//                        }
	    			 
    			} else {
    				$this->_getSession()->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.')); 
                	$this->_getSession()->setCrFormData($this->getRequest()->getParams());  
    				$this->_redirect('*/*');
				  	return;
    			}
    		} catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCrFormData($this->getRequest()->getParams())
                    ->addException($e, $e->getMessage());
                $this->_redirect('*/*');
                return;
            } catch (Exception $e) {
                $this->_getSession()->setCrFormData($this->getRequest()->getParams())
                    ->addException($e, $this->__('Cannot save your catalog request.'));
                 $this->_redirect('*/*');
                 return;
            }
            
    	} else {	 
			$this->_redirect('*/*');
    	
    	}
    	// return $this->_redirectError(Mage::getUrl('*/*'));
    }
    
    public function _getSession()
    {
    	return  Mage::getSingleton('core/session');
    }
    
    public function _isValideCaptcha()
    {
    	$formId = 'user_catalogrequest';
    	
    	$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
     
    	$captchaParams = $this->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
          
    	  if (!$captchaModel->isCorrect($captchaParams[$formId])) { 
                return false;
            } else {
            	return true;
            }
    }
   
}
