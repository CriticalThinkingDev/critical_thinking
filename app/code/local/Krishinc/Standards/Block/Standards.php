<?php
class Krishinc_Standards_Block_Standards extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getStandards()     
     { 
 	    $standards_id = $this->getRequest()->getParam('id');
        return Mage::getModel('standards/standards')->load($standards_id);
    }
    
    public  function getAllStandards()
	{ 
		$standardsCollection = Mage::getModel('standards/standards')->getCollection()->setOrder('pressdate','DESC'); 
		return $standardsCollection; 
	}
	public function isView()
	{
		if($id = Mage::app()->getRequest()->getParam('id')){
			return true;
		}
		 return false;
	}
	public  function getAllStandardProduct()
	{ 
		$standardsCollection = Mage::getModel('standards/standards')->getCollection()
		->addFieldToSelect('product_id');

		$standardsCollection->getSelect()->group(array('product_id'));
		return $standardsCollection->getData(); 
	}
}