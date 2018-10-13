<?php
class Krishinc_Ebooksubscribe_Block_Ebooksubscribe extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getEbooksubscribe()     
     { 
        if (!$this->hasData('ebooksubscribe')) {
            $this->setData('ebooksubscribe', Mage::registry('ebooksubscribe'));
        }
        return $this->getData('ebooksubscribe');
        
    }

    public function getFormActionUrl()
    {
        return $this->getUrl('ebooksubscribe/index/createpost', array('_secure' => true));
    }

}