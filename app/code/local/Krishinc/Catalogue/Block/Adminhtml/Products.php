<?php
class Krishinc_Catalogue_Block_Adminhtml_Products extends Mage_Core_Block_Html_Select
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
		if(!Mage::registry('product_dd'))
		{
			$products = $this->helper('catalogue/data')->getProductsArray();	
			Mage::register('product_dd', $products); 
		}
		
		$products  = Mage::registry('product_dd');
        $this->addOption('', 'Select Product');
        foreach ($products as $action) { 
            $this->addOption($action['value'], $action['label']);
        }

        return parent::_toHtml();
    }

}
