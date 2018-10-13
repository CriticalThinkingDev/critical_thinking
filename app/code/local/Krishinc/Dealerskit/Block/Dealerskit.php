<?php
class Krishinc_Dealerskit_Block_Dealerskit extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getDealerskit()     
     { 
        if (!$this->hasData('dealerskit')) {
            $this->setData('dealerskit', Mage::registry('dealerskit'));
        }
        return $this->getData('dealerskit');
        
    }
}