<?php

class Pisc_Downloadplus_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/*
	 * Constructor
	 */
	  public function __construct()
	  {
	      parent::__construct();
	      $this->setId('downloadplus_logGrid');
	      $this->setDefaultSort('log_id');
	      $this->setDefaultDir('DESC');
	      $this->setSaveParametersInSession(true);
	  }

	 /*
	  * Prepares the Collection
	  */
	  protected function _prepareCollection()
	  {
	      $collection = Mage::getModel('downloadplus/log')->getCollection()
	      	->addDetailsToResult();

	      $this->setCollection($collection);
	      return parent::_prepareCollection();
	  }

	 /*
	  * Updates the Collection with EAV Data
	  */
	  protected function _afterLoadCollection()
	  {
	  	  $items = $this->getCollection()->getItems();
	  	  foreach ($items as $item) {
	  	  		$item->setData('customer_name', '');
	  	  		if ($item->getData('product_id')!='' && $item->getData('product_sku')=='') {
	  	  	  		$product = Mage::getModel('catalog/product');
	  	  	  		$product->load($item->getData('product_id'));
	  	  	  		$item->setData('product_sku', $product->getSku());
	  	  	  		$item->setData('product_name', $product->getName());
	  	  		}
	  	  		if ($item->getData('product_sku')!='' && $item->getData('product_id')=='') {
	  	  	  		$product = Mage::getModel('catalog/product');
	  	  	  		$item->setData('product_id', $product->getIdBySku($item->getData('product_sku')));
	  	  		}
	  	  		if ($item->getData('customer_id')!='' && $item->getData('product_id')!='') {
	  	  	  		$customer = Mage::getModel('customer/customer');
	  	  	  		$customer->load($item->getData('customer_id'));
	  	  	  		$item->setData('customer_name', $customer->getName());
	  	  		}
	  	  		$item->setData('date', date('Y-m-d H:m:s', $item->getData('timestamp')));
	  	  }

	  	  return $this;
	  }

	 /*
	  * Prepare Columns
	  */
	  protected function _prepareColumns()
	  {
	      $this->addColumn('log_id', array(
	        'header'    => Mage::helper('downloadplus')->__('ID'),
	        'align'     =>'right',
	        'width'     => '50px',
	        'index'     => 'log_id'
	      ));

	      $this->addColumn('order_increment_id', array(
	        'header'    => Mage::helper('downloadplus')->__('Order #'),
	        'align'     =>'left',
	      	'index'		=>'order_increment_id'
	      ));

	      $this->addColumn('customer_name', array(
	        'header'    => Mage::helper('downloadplus')->__('Customer'),
	        'align'     =>'left',
	      	'filter'	=>false,
	      	'index'		=>'customer_name'
	      ));

	      $this->addColumn('product_sku', array(
	        'header'    => Mage::helper('downloadplus')->__('SKU'),
	        'align'     =>'left',
	      	'filter'	=>false,
	      	'index'		=>'product_sku'
	      ));

	      $this->addColumn('product_name', array(
	        'header'    => Mage::helper('downloadplus')->__('Product'),
	        'align'     =>'left',
	      	'filter'	=>false,
	      	'index'		=>'product_name'
	      ));

	      $this->addColumn('title', array(
	        'header'    => Mage::helper('downloadplus')->__('Download Title'),
	        'align'     =>'left',
	      	'filter'	=>false,
	      	'index'		=>'title'
	      ));

	      $this->addColumn('ip', array(
	        'header'    => Mage::helper('downloadplus')->__('IP Address'),
	        'align'     =>'left',
	        'index'     => 'ip'
	      ));

	      $this->addColumn('date', array(
	        'header'    => Mage::helper('downloadplus')->__('Date'),
	      	'filter'	=> false,
	        'align'     =>'left',
	        'index'     => 'date'
	      ));

	      return parent::_prepareColumns();
	}

    public function getRowUrl($row)
    {
    	if ($row->getProductId()) {
	        $params = array('id'=>$row->getProductId());
	        if ($this->getRequest()->getParam('store')) {
	            $params['store'] = $this->getRequest()->getParam('store');
	        }
	        $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/catalog/product/edit', $params);
	        return $url;
    	}
    	return false;
    }

}