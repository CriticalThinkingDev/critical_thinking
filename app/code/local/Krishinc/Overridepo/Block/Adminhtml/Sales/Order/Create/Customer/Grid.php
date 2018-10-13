<?php

class Krishinc_Overridepo_Block_Adminhtml_Sales_Order_Create_Customer_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid 
{
 

	protected function _preparePage()
	{
	    $this->getCollection()
	        ->joinAttribute('billing_company', 'customer_address/company', 'default_billing', null, 'left');
	    return parent::_preparePage();
	}
	
	protected function _prepareColumns()
    {
    	 

        $this->addColumn('billing_company', array(
            'header'    =>  Mage::helper('customer')->__('Company'),
            'width'     =>  '150',
            'index'     =>  'billing_company',
            'filter_index'  => 'billing_company',
       
        )); 
        $this->addColumnsOrder('billing_company', 'email');
        return parent::_prepareColumns();
    }
}