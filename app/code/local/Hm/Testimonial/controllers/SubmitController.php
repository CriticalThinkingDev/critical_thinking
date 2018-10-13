<?php

/**
 * Testimonial submit controller
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Hm_Testimonial_SubmitController extends Mage_Core_Controller_Front_Action
{
const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    public function indexAction()
    {
        $this->loadLayout();
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle('Testimonial');                                               
        }           
             
        $this->renderLayout();
    }
    
    public function postAction()
    {

        $data = $this->getRequest()->getPost(); 
        if (!empty($data)) {
        	
        	//$validate1 = $this->_isValideCaptcha();
$validate1 = 1;
			if($validate1)
			{
	            $session = Mage::getSingleton('core/session', array('name'=>'frontend')); 
	               /* @var $session Mage_Core_Model_Session */
	        	if(isset($_FILES['media1']['name']) && $_FILES['media1']['name'] != '') {
					try {	
						/* Starting upload */	
						$uploader = new Varien_File_Uploader('media1');
						
						// Any extention would work
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','avi','flv','swf','mp3','mp4'));
						$uploader->setAllowRenameFiles(false);
						
						// Set the file upload mode 
						// false -> get the file directly in the specified folder
						// true -> get the file in the product like folders 
						//	(file.jpg will go in something like /media/f/i/file.jpg)
						$uploader->setFilesDispersion(false);
								
						// We set media as the upload dir
						$path = Mage::getBaseDir('media').DS.'testimonial'.DS;
						$result= $uploader->save($path, $_FILES['media1']['name'] );
						
						//$data['media'] = 'testimonial/'. $result['file'];
					} catch (Exception $e) {
			      
			        }
		        
			        //this way the name is saved in DB
		  			$data['media'] = 'testimonial/'.$_FILES['media1']['name'];
				}else {
					if(isset($data['media']['delete']) && $data['media']['delete'] == 1) {
						 $data['media'] = '';
					} else {
						unset($data['media']);
					}
				}
            
 
	            // Set store view
	            if (!Mage::app()->isSingleStoreMode()) {
	            	$data['stores'] = array(Mage::app()->getStore()->getId());
	            }
	            
	            $testimonial     = Mage::getModel('testimonial/testimonial')->setData($data);
				
	            /* @var $review Mage_Review_Model_Review */
	                        
	            // set CreatedTime and UpdateTime value:
	        	if ($testimonial->getCreatedTime == NULL || $testimonial->getUpdateTime() == NULL) {
					$testimonial->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$testimonial->setUpdateTime(now());
				}
				
	            $validate = $testimonial->validate();
	            if ($validate === true) {
	                try {
	                    $testimonial->save();
$translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
     $mailTemplate = Mage::getModel('core/email_template');
                        /* @var $mailTemplate Mage_Core_Model_Email_Template */
                        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                            ->setReplyTo($data['email'])
                            ->sendTransactional(
                                'testimonial_email_email_template',
                                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),

                                'marketing@criticalthinking.com',
                                null,
                                array('data' => $testimonial)
                            );

                        if (!$mailTemplate->getSentSuccess()) {
                            throw new Exception();
                        }

                        $translate->setTranslateInline(true);
	                    $session->addSuccess($this->__('Your testimonial has been accepted for moderation'));
	                    $session->setFormData('');
                        $this->_redirect('*/*'); 
	                } 
	                catch (Exception $e) {
	                    $session->setFormData($data);
	                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
                        $this->_redirect('*/*'); 
	                }
	            }
	            else {
	                try{
	              		$session->setFormData($data);
	                }
	                catch(Exception $e){
	                    Mage::log($e->getMessage());
	                }                  
	                if (is_array($validate)) {                   
	                    foreach ($validate as $errorMessage) {
	                        $session->addError($errorMessage);
	                    }                 
	                }
	                else {
	                	 $session->setFormData($data);
	                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
	                }
	            }
	        } else {
				$this->_getSession()->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.')); 
            	$this->_getSession()->setFormData($this->getRequest()->getParams());  
				$this->_redirect('*/*'); 
			  	return;
      		}
        } 
		//$this->_redirect('*/*/');
        if ($redirectUrl = Mage::getSingleton('core/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
    } 
    
//    public function postAction()
//    {
//
//        $data = $this->getRequest()->getPost();
//		//var_dump($data);
//        //var_dump($_FILES['media1']['name']); exit;
//        if (!empty($data)) {
//            $session = Mage::getSingleton('core/session', array('name'=>'frontend'));
//            /* @var $session Mage_Core_Model_Session */
//        	if(isset($_FILES['media1']['name']) && $_FILES['media1']['name'] != '') {
//				try {	
//					/* Starting upload */	
//					$uploader = new Varien_File_Uploader('media1');
//					
//					// Any extention would work
//	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','avi','flv','swf','mp3','mp4'));
//					$uploader->setAllowRenameFiles(false);
//					
//					// Set the file upload mode 
//					// false -> get the file directly in the specified folder
//					// true -> get the file in the product like folders 
//					//	(file.jpg will go in something like /media/f/i/file.jpg)
//					$uploader->setFilesDispersion(false);
//							
//					// We set media as the upload dir
//					$path = Mage::getBaseDir('media').DS.'testimonial'.DS;
//					$result= $uploader->save($path, $_FILES['media1']['name'] );
//					
//					//$data['media'] = 'testimonial/'. $result['file'];
//				} catch (Exception $e) {
//		      
//		        }
//	        
//		        //this way the name is saved in DB
//	  			$data['media'] = 'testimonial/'.$_FILES['media1']['name'];
//			}else {
//				if(isset($data['media']['delete']) && $data['media']['delete'] == 1) {
//					 $data['media'] = '';
//				} else {
//					unset($data['media']);
//				}
//			}
//            
//            // Set store view
//            if (!Mage::app()->isSingleStoreMode()) {
//            	$data['stores'] = array(Mage::app()->getStore()->getId());
//            }
//            
//            $testimonial     = Mage::getModel('testimonial/testimonial')->setData($data);
//			
//            /* @var $review Mage_Review_Model_Review */
//                        
//            // set CreatedTime and UpdateTime value:
//        	if ($testimonial->getCreatedTime == NULL || $testimonial->getUpdateTime() == NULL) {
//				$testimonial->setCreatedTime(now())
//					->setUpdateTime(now());
//			} else {
//				$testimonial->setUpdateTime(now());
//			}
//			
//            $validate = $testimonial->validate();
//            if ($validate === true) {
//                try {
//                    $testimonial->save();
//                    $session->addSuccess($this->__('Your testimonial has been accepted for moderation'));
//                }
//                catch (Exception $e) {
//                    $session->setFormData($data);
//                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
//                }
//            }
//            else {
//                try{
//                $session->setFormData($data);
//                }
//                catch(Exception $e){
//                    Mage::log($e->getMessage());
//                }                  
//                if (is_array($validate)) {                   
//                    foreach ($validate as $errorMessage) {
//                        $session->addError($errorMessage);
//                    }                 
//                }
//                else {
//                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
//                }
//            }
//        }
//		//$this->_redirect('*/*/');
//        if ($redirectUrl = Mage::getSingleton('core/session')->getRedirectUrl(true)) {
//            $this->_redirectUrl($redirectUrl);
//            return;
//        }
//        $this->_redirectReferer();
//    }
    
    
     
    public function _getSession()
    {
    	return  Mage::getSingleton('core/session');
    }
    
    public function _isValideCaptcha()
    {
    	$formId = 'testimonial';
    	
    	$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
     
    	$captchaParams = $this->getRequest()->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
          
    	  if (!$captchaModel->isCorrect($captchaParams[$formId])) { 
                return false;
            } else {
            	return true;
            }
    }
}
