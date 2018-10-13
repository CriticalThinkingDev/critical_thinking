<?php
class Krishinc_Fieldtester_Block_Fieldtester extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFieldtester()     
     { 
        if (!$this->hasData('fieldtester')) {
            $this->setData('fieldtester', Mage::registry('fieldtester'));
        }
        return $this->getData('fieldtester');
        
    }
}