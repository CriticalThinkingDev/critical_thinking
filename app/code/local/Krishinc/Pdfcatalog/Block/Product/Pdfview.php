<?php

class Krishinc_Pdfcatalog_Block_Product_Pdfview extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {  
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getProduct()->getMetaTitle());
        }
        return parent::_prepareLayout();
    }
    
    public function getProduct()
    {
        return Mage::registry('product');
    }

}