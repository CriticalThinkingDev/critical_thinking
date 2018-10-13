<?php

class Krishinc_Pdfcatalog_Block_Product_License extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {   
        return parent::_prepareLayout();
    }
    
    public function getProduct()
    {
    	if($productID = $this->getRequest()->getParam('id'))
    	{
    		return  Mage::getModel('catalog/product')->load($productID);
    	} 
    	return '';
    }

}