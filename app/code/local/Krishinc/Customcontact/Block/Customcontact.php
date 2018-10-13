<?php
class Krishinc_Customcontact_Block_Customcontact extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCustomcontact()     
     { 
        if (!$this->hasData('customcontact')) {
            $this->setData('customcontact', Mage::registry('customcontact'));
        }
        return $this->getData('customcontact');
        
    }
}