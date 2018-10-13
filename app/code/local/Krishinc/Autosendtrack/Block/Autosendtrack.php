<?php
class Krishinc_Autosendtrack_Block_Autosendtrack extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAutosendtrack()     
     { 
        if (!$this->hasData('autosendtrack')) {
            $this->setData('autosendtrack', Mage::registry('autosendtrack'));
        }
        return $this->getData('autosendtrack');
        
    }
}