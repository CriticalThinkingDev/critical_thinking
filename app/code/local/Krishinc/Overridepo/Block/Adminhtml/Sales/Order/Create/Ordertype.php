<?php
 
class Krishinc_Overridepo_Block_Adminhtml_Sales_Order_Create_Ordertype extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function getOrderType()
    { 
        return $this->htmlEscape($this->getQuote()->getOrderType()); 
    }

    public function getAllOptions()
    {
    	return  Krishinc_Overridepo_Model_Adminhtml_Ordertype::getOptionArray();  
    }
}
