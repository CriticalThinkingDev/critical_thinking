<?php
class Krishinc_Educent_Block_Educent extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getEducent()     
     { 
        if (!$this->hasData('educent')) {
            $this->setData('educent', Mage::registry('educent'));
        }
        return $this->getData('educent');
        
    }
}