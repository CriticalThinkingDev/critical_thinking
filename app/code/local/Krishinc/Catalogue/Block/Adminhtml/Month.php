<?php
class Krishinc_Catalogue_Block_Adminhtml_Month extends Mage_Core_Block_Html_Select
{
    protected $_options = array();

    public function setInputName($value)
    {
        return $this->setName($value);
    }
	/*
	* Set category Array data for all dynamic rate add
	*/
    public function _toHtml()
    {
		if(!Mage::registry('month_dd'))
		{
			$months = $this->helper('catalogue/data')->getMonthArray();	
			Mage::register('month_dd', $months); 
		}
		
		$months  = Mage::registry('month_dd');
        $this->addOption('', 'Select Month');
        foreach ($months as $action) { 
            $this->addOption($action['value'], $action['label']);
        }

        return parent::_toHtml();
    }


}
