<?php

class Krishinc_Overridepo_Block_Adminhtml_Sales_Order_Create_Search_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid  
{
    public function __construct()
    {
        parent::__construct(); 
        $this->setDefaultSort('sku'); 
        $this->setDefaultDir('asc');
    }
    
    /**
     * Prepare collection to be displayed in the grid
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareCollection()
    {
    	 
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection
            ->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('series') 
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', array_keys(
                Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
            ))
            ->addAttributeToSelect('gift_message_available');

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
		$collection->addAttributeToFilter('status', array('eq' => '1')); 
		$collection->addAttributeToFilter('series',array('eq' => '0')); 
		
        $this->setCollection($collection); 
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        return $this;
    }
    
     /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns()
    {
    		$this->addColumn('special_price', array(
            'header'    => Mage::helper('sales')->__('Sale Price'),
            'column_css_class' => 'special_price',
            'align'     => 'center',
            'type'      => 'currency',
            'sortable'      => false,
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'special_price',
           
        ));
         $this->addColumnsOrder('special_price', 'price');
          return parent::_prepareColumns();
    }
}