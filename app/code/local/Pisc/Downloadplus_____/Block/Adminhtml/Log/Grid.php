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
	  	  		$item->setData('customer_name', null);
	  	  		if ($item->getData('product_id') && !$item->getData('product_sku')) {
	  	  	  		$product = Mage::getModel('catalog/product');
	  	  	  		$product->load($item->getData('product_id'));
	  	  	  		$item->setData('product_sku', $product->getSku());
	  	  	  		$item->setData('product_name', $product->getName());
	  	  		}
	  	  		if ($item->getData('product_sku') && !$item->getData('product_id')=='') {
	  	  	  		$product = Mage::getModel('catalog/product');
	  	  	  		$item->setData('product_id', $product->getIdBySku($item->getData('product_sku')));
	  	  		}
	  	  		if ($item->getData('customer_id') && !$item->getData('customer_name')) {
	  	  	  		$customer = Mage::getModel('customer/customer');
	  	  	  		$customer->load($item->getData('customer_id'));
	  	  	  		$item->setData('customer_name', $customer->getName());
	  	  		}
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
	        'align'     => 'right',
	        'width'     => '50px',
      		'type'		=> 'number',
	        'index'     => 'log_id'
	      ));

	      $this->addColumn('order_increment_id', array(
	        'header'    => Mage::helper('downloadplus')->__('Order #'),
	        'align'     => 'left',
      		'type'		=> 'number',
	      	'index'		=> 'order_increment_id',
	      	'renderer'  => 'downloadplus/adminhtml_renderer_grid_column_ordernumber'
	      ));

	      $this->addColumn('customer_name', array(
	        'header'    => Mage::helper('downloadplus')->__('Customer'),
	        'align'     => 'left',
	      	'filter'	=> false,
      		'sortable'  => false,
      		'type'		=> 'text',
	      	'index'		=> 'customer_name',
	      	'renderer'  => 'downloadplus/adminhtml_renderer_grid_column_customer_account'
	      ));

	      $this->addColumn('product_sku', array(
	        'header'    => Mage::helper('downloadplus')->__('SKU'),
	        'align'     => 'left',
	      	'filter'	=> false,
	      	'sortable'	=> false,
      		'type'		=> 'text',
	      	'index'		=> 'product_sku'
	      ));

	      $this->addColumn('product_name', array(
	        'header'    => Mage::helper('downloadplus')->__('Product'),
	        'align'     => 'left',
	      	'filter'	=> false,
	      	'sortable'	=> false,
      		'type'		=> 'text',
	      	'index'		=> 'product_name'
	      ));

	      $this->addColumn('title', array(
	        'header'    => Mage::helper('downloadplus')->__('Download Title'),
	        'align'     => 'left',
	      	'filter'	=> false,
	      	'sortable'	=> false,
      		'type'		=> 'text',
	      	'index'		=> 'title',
	      	'renderer'  => 'downloadplus/adminhtml_renderer_grid_column_nohtml'
	      ));

	      $this->addColumn('type', array(
	      		'header'    => Mage::helper('downloadplus')->__('Download Type'),
	      		'align'     => 'left',
	      		'filter'	=> false,
	      		'sortable'	=> false,
	      		'index'     => 'type',
	      		'type'		=> 'options',
	      		'options'   => array(
	      				'link-purchased-item' => Mage::helper('downloadplus')->__('Purchased Download'),
	      				'link-sample' => Mage::helper('downloadplus')->__('Sample of purchaseable Download'),
	      				'sample' => Mage::helper('downloadplus')->__('Sample'),
	      				'product-link' => Mage::helper('downloadplus')->__('Additional Product Download'),
	      				'customer-link' => Mage::helper('downloadplus')->__('Additional Customer Download'),
	      		)
	      ));

	      $this->addColumn('ip', array(
	        'header'    => Mage::helper('downloadplus')->__('IP Address'),
	        'align'     => 'left',
	      	'sortable'	=> true,
      		'type'		=> 'text',
	      	'index'     => 'ip'
	      ));

	      $this->addColumn('timestamp', array(
	        'header'    => Mage::helper('downloadplus')->__('Date'),
	      	'sortable'  => true,
	        'align'     => 'left',
	      	'type'		=> 'datetime',
	        'index'     => 'timestamp'
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
	        $url = Mage::helper("adminhtml")->getUrl('adminhtml/catalog_product/edit', $params);
	        return $url;
    	}
    	return false;
    }

}