<?php
class Krishinc_Addressupdate_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    { 
 
		$this->loadLayout();     
		$this->renderLayout();
    }
    
     public function postAction()
    {
    	if($data = $this->getRequest()->getPost())
    	{
    	 
    		try {   
    			$helper = Mage::helper('addressupdate');
				$helper->sendMail($data);  
    			$this->_getSession()->unsFormData(''); 
    			//$this->_getSession()->setFormData('success','true'); 
    			$this->_getSession()->addSuccess('Thank you for updating our records!');
				$this->_redirect('*/*');
			  	return; 
    			 
    		} catch (Mage_Core_Exception $e) {
                $this->_getSession()->setFormData($this->getRequest()->getParams())
                    ->addException($e, $e->getMessage());
                $this->_redirect('*/*');
                return;
            } catch (Exception $e) { 
                $this->_getSession()->setFormData($this->getRequest()->getParams())
                    ->addException($e, $this->__('Cannot send your request.'));
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
}