<?php
class Krishinc_Free_IndexController extends Mage_Core_Controller_Front_Action
{
	const PREDEFINE_ACCESS_CODE = 'DONATION123';
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
			if(strtolower($data['access_code']) == strtolower(self::PREDEFINE_ACCESS_CODE))
			{
				Mage::getSingleton('core/session')->setFreeBook(true);
				Mage::getSingleton('core/session')->setRedirectUrl(Mage::getUrl('').'donation');
					$url = Mage::getUrl('').'download'; 
				$this->_redirectUrl($url);
			} else {
				Mage::getSingleton('core/session')->addError(Mage::helper('free')->__('Invalid Name/Access Code'));  
				Mage::getSingleton('core/session')->setFormData($data);  
				$url = Mage::getUrl('').'donation'; 
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