<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Adminhtml Customer Edit Downloads Purchased Links block
 *
 * @category   Pisc
 * @package    Pisc_Downdloadplus
 * @author
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Edit_Tab_Downloads_Purchasedlinks extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('purchasedDownloadableLinksGrid');
        //$this->setTemplate('downloadplus/product/edit/detail/grid.phtml');

        $this->setEmptyText($this->__('There are no purchased Downloadable Products for this Customer.'));
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
    	$collection = Mage::getSingleton('downloadplus/customer_download')
    					->setCustomer($this->getCustomer())
    					->getLinkPurchasedItemCollection();

	    $this->setCollection($collection);

	    return $this;
    }

	/*
	 * Updates the Collection
	 */
	protected function _afterLoadCollection()
	{
	  	return $this;
	}

    protected function _prepareColumns()
    {

    	$this->addColumn('order_increment_id', array(
            'header'    => $this->__('Order #'),
            'sortable'  => false,
            'index'     => 'order_increment_id',
    		'type'		=> 'number',
    		'renderer'	=> 'Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Ordernumber'
        ));

    	$this->addColumn('product_sku', array(
            'header'    => $this->__('SKU'),
            'sortable'  => false,
            'index'     => 'product_sku',
    		'type'		=> 'text'
        ));

        $this->addColumn('product_title', array(
            'header'    => $this->__('Product'),
            'sortable'  => false,
            'index'     => 'product_name',
        	'type'		=> 'text'
        ));

        $this->addColumn('link_title', array(
            'header'    => $this->__('Download'),
            'sortable'  => false,
            'index'     => 'link_title',
        	'type'		=> 'text',
       		'renderer'	=> 'Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Link_Title'
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'sortable'  => false,
            'index'     => 'status',
        	'type'		=> 'select',
        	'options'	=> Mage::getModel('downloadplus/system_config_source_download_settings_status')->toOptions(true),
        	'renderer'	=> 'Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Status'
        ));

        $this->addColumn('downloads_used', array(
            'header'    => $this->__('Downloads'),
            'sortable'  => false,
            'index'     => 'number_of_downloads_used',
            'type'      => 'number',
        	'renderer'	=> 'Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Downloads'
        ));

        if (Mage::registry('downloadplus_adminhtml_customer_edit_tab_downloads_form_elements')) {
	        $this->addColumn('expires_on', array(
	            'header'    => $this->__('Expires on'),
	            'sortable'  => false,
	            'index'     => 'expires_on',
	        	'type'		=> 'form',
	        	'renderer'  => 'Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Expires'
	        ));
        } else {
	        $this->addColumn('expires_on', array(
	            'header'    => $this->__('Expires on'),
	            'sortable'  => false,
	            'index'     => 'expires_on',
	        	'renderer'  => 'Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Expires'
	        ));
        }

        $this->addColumn('newest_download_timestamp', array(
            'header'    => $this->__('Recently Downloaded on'),
            'sortable'  => false,
            'index'     => 'newest_download_timestamp',
            'type'      => 'datetime'
        ));

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
		        return $this->getUrl('adminhtml/downloadplus_redirect/viewOrder', $params);
	    	}
        }
    	return false;
    }

}
