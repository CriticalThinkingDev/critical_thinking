<?php
class Krishinc_Free_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		 
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function downloadAction()
	{
		if($data = $this->getRequest()->getParams())
		{
			$helper = Mage::helper('free');
			if($result = $helper->validateOrder($data))
			{
				if($result['error']) {
					Mage::getSingleton('core/session')->addError($result['message']);                 	
					Mage::getSingleton('core/session')->setFormData($data);  
					$url = Mage::getUrl('').'free'; 
					$this->_redirectUrl($url);
				} else {
					Mage::getSingleton('core/session')->setFreeBook(true);
					Mage::getSingleton('core/session')->setRedirectUrl(Mage::getUrl('').'free');
						$url = Mage::getUrl('').'download'; 
					$this->_redirectUrl($url);
					 
				}
			}
		}
	}
	
	public function donationAction()
	{
		if($data = $this->getRequest()->getParams())
		{   
			if(strtolower($data['access_code']) == strtolower(Mage::helper('free')->getAccessCode()))
			{ 
                $model = Mage::getModel('free/free');		
                $model->setData($data);
				$model->setDonationAt(now());
                try {                                    
                    $model->save();                                    
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());                                    
                }
				Mage::getSingleton('core/session')->setFreeBook(true);
				Mage::getSingleton('core/session')->setRedirectUrl(Mage::getUrl('').'donations');
                $url = Mage::getUrl('').'donations-download'; 
				$this->_redirectUrl($url);
			} else { 
				Mage::getSingleton('core/session')->addError(Mage::helper('free')->__('Invalid Name/Access Code'));  
				Mage::getSingleton('core/session')->setFormData($data);  
				$url = Mage::getUrl('').'donations'; 
				$this->_redirectUrl($url);
				 
			}
		}
	}
	
//	public  function donationAction()
//	{
//		$this->loadLayout();
//		$this->renderLayout();
//	}
	 
}
