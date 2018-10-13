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
    	$collection = Mage::getModel('downloadplus/customer_download')
    					->setCustomer($this->getCustomer())
    					->getLinkPurchasedItemCollection();

	    $this->setCollection($collection);

  		$items = $this->getCollection()->getItems();
  	  	foreach ($items as $item) {
  	  		$object = Mage::getModel('catalog/product')->load($item->getProductId());
  	  		$item->setData('product_sku', $object->getSku());
  	  		$item->setData('product_name', $object->getName());
  	  		$item->setData('newest_download_date', $this->formatTimestamp($item->getData('newest_download_timestamp'), 'medium', true));

  	  		$object = Mage::getModel('downloadable/link_purchased')->load($item->getPurchasedId());
  	  		$item->setData('order_increment_id', $object->getOrderIncrementId());
  	  		
  	  		$object = Mage::getModel('downloadplus/link_purchased_item_extension')->loadByPurchasedLink($item);
  	  		if ($object->getId()) {
  	  			$item->setData('expires_on', $object->getExpiresOn());
  	  			/*
  	  			$days = $object->getDaysUntilExpiration();
  	  			$item->setData('expires_on', Mage::helper('core')->formatDate($object->getExpiresOn(), 'short', false));
  	  			if ($days) {
  	  				$item->setData('expires_on', $item->getData('expires_on').' ('.$days.'d)');
  	  			}
  	  			if ($object->isExpired()) {
  	  				$item->setData('status', Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED);
  	  			}*/
  	  		} else {
  	  			$item->setData('expires_on', null);
  	  		}
  	  	}

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
    		'type'		=> 'number'
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
        	'type'		=> 'text'
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
        
        $this->addColumn('date', array(
            'header'    => $this->__('Recently Downloaded on'),
            'sortable'  => false,
            'index'     => 'newest_download_date',
            'type'      => 'text'
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
		        return $this->getUrl('downloadplusadmin/redirect/viewOrder', $params);
	    	}
        }
    	return false;
    }

}