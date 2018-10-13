<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Products Serialnumber List for Customer
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.3
 */

class Pisc_Downloadplus_Block_Customer_Products_Serialnumber extends Mage_Core_Block_Template
{
	protected $_sortProducts = 'product_name ASC';
	protected $_sortSerialItems = 'serial_id DESC';
	protected $_session = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_session = Mage::getSingleton('customer/session');

        $this->updateCollection();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->updateLayout(true);

        return $this;
    }

    public function updateLayout($create=false)
    {
    	if ($this->getItems()) {
    		
	    	$this->unsetChild('pager');
	    	
	    	if ($create) {
		        $pager = $this->getLayout()->createBlock('page/html_pager', 'downloadplus.customer.serialnumbers.pager')
		            ->setCollection($this->getItems());
	    	} else {
	    		$pager = $this->getLayout()->getBlock('downloadplus.customer.serialnumbers.pager')->setCollection($this->getItems());
	    	}
	        $this->setChild('pager', $pager);
	
	        $this->getItems()->load();
	        foreach ($this->getItems() as $item) {
	        	if ($item->getPurchasedId()) {
	        		$item->setPurchased($this->getPurchased()->getItemById($item->getPurchasedId()));
	        		$item->setProductName($item->getLinkPurchased()->getProductName());
	        		$item->setProductSku($item->getLinkPurchased()->getProductSku());
	        	} else {
	        		$data = new Varien_Object();
	        		$item->setCustomerId($this->_session->getCustomerId());
	        		$orderItem = Mage::getModel('sales/order_item')->load($item->getOrderItemId());
	        		if ($orderItem->getId()) {
	        			$data->setOrderItemId($orderItem->getId());
	        			$data->setOrderId($orderItem->getOrderId());
	        			$data->setOrderIncrementId($orderItem->getOrder()->getIncrementId());
	        			$data->setProductId($orderItem->getProductId());
	
	        			$product = Mage::getModel('catalog/product')->load($orderItem->getProductId());
		        		$item->setProductSku($product->getSku());
		        		$item->setProductName($product->getName());
	        		}
	        		$item->setPurchased($data);
	        	}
	
	        }
    	}

        return $this;
    }

    public function updateCollection()
    {
    	$this->setItems(Array());
    	
    	// Downloads related to Downloadable Items
        $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
            ->addFieldToFilter('customer_id', $this->_session->getCustomerId())
            ->addOrder('created_at', 'desc');
        $this->setPurchased($purchased);

        $purchasedIds = Array();
        foreach ($purchased as $_item) {
            $purchasedIds[] = $_item->getId();
        }

        // Downloads related to Order Items
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('customer_id', $this->_session->getCustomerId())
            ->addAttributeToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->addAttributeToSort('created_at', 'desc')
        ;

        $orderItemsIds = Array();
        foreach ($orders as $order) {
        	$items = $order->getAllItems();
        	foreach ($items as $item) {
        		$orderItemsIds[] = $item->getId();
        	}
        }

        if (count($purchasedIds) || count($orderItemsIds)) {

	        $items = Mage::getResourceModel('downloadplus/link_purchased_item_serialnumber_collection');

	        $items->getSelect()
	        		->where('main_table.purchased_id IN (?)', $purchasedIds)
	        		->orWhere('main_table.order_item_id IN (?)', $orderItemsIds);
/*
	        $items->addFieldToFilter('main_table.status', array('nin' => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT))
	          	->addPurchasedLinksToResult();
*/
	        $items->addPurchasedLinksToResult();
	        
	        $items->getSelect()
	          	->order(Array($this->_sortProducts, $this->_sortSerialItems));

	        $this->setItems($items);

        }

        return $this;
    }

	/*
	 * Sets a Sort Condition
	 */
	public function setSortProducts($sort) {
		$this->_sortProducts = $sort;

		return $this;
	}

	/*
	 * Sets a Sort Condition
	 */
	public function setSortSerialItems($sort) {
		$this->_sortSerialItems = $sort;

		return $this;
	}

    public function getOrderViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $orderId));
    }

    public function getDownloadUrl($serial)
    {
    	$params = Array();
    	if ($serial instanceof Pisc_Downloadplus_Model_Link_Purchased_Item_Serialnumber) {
    		$params = Array('id' => $serial->getSerialHash());
    	} else {
    		if ($_serial = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->load($serial)) {
    			$params = Array('id' => $_serial->getSerialHash());
    		}
    	}

    	$config = Mage::getModel('downloadplus/config');
    	if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}
    	return $this->getUrl('downloadable/download/serialnumber', $params);
    }

    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /*
     * Returns true if serialnumbers are downloadable
     */
    public function isDownloadable()
    {
    	$config = Mage::getModel('downloadplus/config');
    	return $config->isDownloadSerialnumbers();
    }

	/**
	 * Returns true if the RSS Feed is available
	 */
	public function isRssAvailable()
	{
		$config = Mage::getModel('downloadplus/config');
		return $config->isDownloadableRssFeed();
	}

	/**
	 * Returns the Link to the RSS Feed of the Version History
	 */
	public function getRssLink()
	{
		return $this->getUrl('downloadable/rss/updates');
	}

}
