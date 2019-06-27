<?php
class Siteguarding_Antivirus_Adminhtml_AntivirusbackendController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('antivirus/antivirusbackend');
		//return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Antivirus"));
	   $this->renderLayout();
    }
}