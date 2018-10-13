<?php
/**
 * Customer Download Log Grid
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Edit_Tab_Downloads_Log extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('trackedDownloads');
        //$this->setTemplate('downloadplus/product/edit/detail/grid.phtml');

        $this->setEmptyText($this->__('There are no entries available at the moment'));
        $this->setSortable(false);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    /*
     * Returns the current Product
     */
    protected function getCustomer()
    {
		return Mage::registry('current_customer');
    }

    protected function _prepareCollection()
    {
    	$collection = Mage::getModel('downloadplus/log')->getCollection()
    					->addFieldToFilter('main_table.customer_id', Array('eq'=>$this->getCustomer()->getId()))
    					->setOrder('timestamp')
    					->addDetailsToResult();

	    $this->setCollection($collection);

	    return $this;
    }

	/*
	 * Updates the Collection
	 */
	protected function _afterLoadCollection()
	{
		$items = $this->getCollection()->getItems();
		foreach ($items as $item) {
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
		}
		
		return $this;
	}

    protected function _prepareColumns()
    {
    	$this->addColumn('order_increment_id', array(
    			'header'    => Mage::helper('downloadplus')->__('Order #'),
    			'align'     => 'left',
    			'index'		=> 'order_increment_id',
    			'renderer'  => 'downloadplus/adminhtml_renderer_grid_column_ordernumber'
    	));
    	
    	$this->addColumn('product_sku', array(
    			'header'    => Mage::helper('downloadplus')->__('SKU'),
    			'align'     => 'left',
    			'filter'	=> false,
    			'sortable'  => false,
    			'index'		=> 'product_sku'
    	));
    	
    	$this->addColumn('product_name', array(
    			'header'    => Mage::helper('downloadplus')->__('Product'),
    			'align'     => 'left',
    			'filter'	=> false,
    			'sortable'  => false,
    			'index'		=> 'product_name'
    	));
    	
    	$this->addColumn('title', array(
    			'header'    => Mage::helper('downloadplus')->__('Download Title'),
    			'align'     => 'left',
    			'filter'	=> false,
    			'sortable'  => false,
    			'index'		=> 'title',
    			'renderer'  => 'downloadplus/adminhtml_renderer_grid_column_nohtml'
    	));
    	
    	$this->addColumn('type', array(
    			'header'    => Mage::helper('downloadplus')->__('Download Type'),
    			'align'     => 'left',
    			'filter'	=> false,
    			'sortable'  => false,
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
    			'index'     => 'ip'
    	));
    	
    	$this->addColumn('timestamp', array(
    			'header'    => Mage::helper('downloadplus')->__('Date'),
    			'filter'	=> false,
    			'align'     => 'left',
    			'type'		=> 'datetime',
    			'index'     => 'timestamp'
    	));
    	
    	return parent::_prepareColumns();
    }

    protected function _preparePage()
    {
    	// Empty to override parent function
    }

    protected function formatTimestamp($date=null, $format='short', $showTime=false)
    {
    	if ($date) {
    		$zendDate = new Zend_Date($date);
    		return parent::formatDate($zendDate, $format, $showTime);
    	}
    	return '';
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
	    	if ($row->getOrderIncrementId()) {
		        $params = array('order'=>$row->getOrderIncrementId());
		        if ($this->getRequest()->getParam('store')) {
		            $params['store'] = $this->getRequest()->getParam('store');
		        }
		        return $this->getUrl('downloadplusadmin/redirect/viewOrder', $params);
	    	}
        }
    	return false;
    }

}