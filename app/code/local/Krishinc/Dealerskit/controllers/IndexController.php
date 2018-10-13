<?php
class Krishinc_Dealerskit_IndexController extends Mage_Core_Controller_Front_Action
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
    			if($validate)
    			{
    				 
	    			$model = Mage::getModel('dealerskit/dealerskit');
	    			 
//	    				$model->setDealerskitId($model->isEmailExists($data['email']));
	    			 
		    			$model->setData($data)
		    				  ->setStatus('2');
		    			
						if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
							$model->setCreatedTime(now())
								->setUpdateTime(now());
						} else { 
							$model->setUpdateTime(now());
						}	 
						
						$model->save();	 
						$model->sendMail($model->getData());//To Send email
				    	 Mage::dispatchEvent('dealerskit_index_createpost_after', array('requestdata' => $model));  // Dispatch event to add entry in listrak email champaign system 
						
		    			$this->_getSession()->unsDrFormData(''); 
		    			$this->_getSession()->setDrFormData('success','true'); 
		    			$this->_getSession()->addSuccess('Your inquiry was submitted and a Reseller Information Packet will be mailed to you as soon as possible.');
						$this->_redirect('*/*');
					  	return;
	    			 
    			} else {
    				$this->_getSession()->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.')); 
                	$this->_getSession()->setDrFormData($this->getRequest()->getParams());  
    				$this->_redirect('*/*');
				  	return;
    			}
    		} catch (Mage_Core_Exception $e) {
                $this->_getSession()->setDrFormData($this->getRequest()->getParams())
                    ->addException($e, $e->getMessage());
                $this->_redirect('*/*');
                return;
            } catch (Exception $e) {
                $this->_getSession()->setDrFormData($this->getRequest()->getParams())
                    ->addException($e, $this->__('Cannot save your dealers kit request.'));
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
    	$formId = 'user_dealerskit';
    	
    	$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
     
    	$captchaParams = $this->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
          
    	  if (!$captchaModel->isCorrect($captchaParams[$formId])) { 
                return false;
            } else {
            	return true;
            }
    }
   
}