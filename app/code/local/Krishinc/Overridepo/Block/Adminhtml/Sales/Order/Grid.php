<?php

class Krishinc_Overridepo_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
	protected function _prepareColumns()
    {
    	 
        $options = Krishinc_Overridepo_Model_Adminhtml_Ordertype::getOptionArray(); 
        $this->addColumn('order_type', array(
            'header'    =>  Mage::helper('customer')->__('Order Type'),
            'width'     =>  '100',
            'index'     =>  'order_type',
            'type'      =>  'options',
            'options'   =>   $options
        ));
        $this->addColumn('billing_company', array(
            'header'    =>  Mage::helper('customer')->__('Company'),
            'width'     =>  '150',
            'index'     =>  'billing_company',
       
        ));
        $this->addColumnsOrder('order_type', 'grand_total');
        $this->addColumnsOrder('company', 'created_at');  
        return parent::_prepareColumns();
    }
}