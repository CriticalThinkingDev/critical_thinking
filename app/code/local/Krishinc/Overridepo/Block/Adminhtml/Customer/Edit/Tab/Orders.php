<?php

class Krishinc_Overridepo_Block_Adminhtml_Customer_Edit_Tab_Orders extends Mage_Adminhtml_Block_Customer_Edit_Tab_Orders
{
	 protected function _prepareCollection()
    { 	parent::_prepareCollection();
    	 $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('order_currency_code')
            ->addFieldToSelect('store_id')
            ->addFieldToSelect('billing_name')
            ->addFieldToSelect('billing_company')
            ->addFieldToSelect('shipping_name')
            ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
            ->setIsCustomerMode(true);

        $this->setCollection($collection);
        return $this; 
    }
	  
	protected function _prepareColumns()
    {
    	 

        $this->addColumn('billing_company', array(
            'header'    =>  Mage::helper('customer')->__('Company'),
            'width'     =>  '150',
            'index'     =>  'billing_company',
       
        )); 
        $this->addColumnsOrder('company', 'created_at');
        return parent::_prepareColumns();
    }
}