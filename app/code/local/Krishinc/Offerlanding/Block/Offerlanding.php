<?php
class Krishinc_Offerlanding_Block_Offerlanding extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getOfferlanding()     
     { 
        if (!$this->hasData('offerlanding')) {
            $this->setData('offerlanding', Mage::registry('offerlanding'));
        }
        return $this->getData('offerlanding');
        
    }
}