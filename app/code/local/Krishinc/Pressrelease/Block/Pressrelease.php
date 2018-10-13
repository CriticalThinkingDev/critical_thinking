<?php
class Krishinc_Pressrelease_Block_Pressrelease extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPressrelease()     
     { 
 	    $pressrelease_id = $this->getRequest()->getParam('id');
        return Mage::getModel('pressrelease/pressrelease')->load($pressrelease_id);
    }
    
    public  function getAllPressrelease()
	{ 
		$pressreleaseCollection = Mage::getModel('pressrelease/pressrelease')->getCollection()->setOrder('pressdate','DESC'); 
		return $pressreleaseCollection; 
	}
	public function isView()
	{
		if($id = Mage::app()->getRequest()->getParam('id')){
			return true;
		}
		 return false;
	}
}