<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Customer Download model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.1
 */

class Pisc_Downloadplus_Model_Customer_Download extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_customer_download';

	protected $_customer = null;
	protected $_purchased = null;
	protected $_items = null;

    /**
     * Constructor
     *
     */
    public function _construct()
    {
        parent::_construct();
    }

    public function setCustomer($customer)
    {
    	$this->_customer = $customer;
    	return $this;
    }

    /**
     * Retrieve base temporary path
     *
     * @return string
     */
    public static function getBaseTmpPath($customer=null)
    {
    	$result = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'customer';
    	if ($customer && $customer->getId()) {
    		$result.= DS . $customer->getId();
    	}
    	// Create the path if it not already exists
    	if (!file_exists($result)) {
    		@mkdir($result, 0770, true);
    	}
        return $result;
    }

    /**
     * Retrieve Base files path
     *
     * @return string
     */
    public static function getBasePath($customer=null)
    {
    	$result = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'customer' . DS . 'links';
    	if ($customer && $customer->getId()) {
    		$result.= DS . $customer->getId();
    	}
    	// Create the path if it not already exists
    	if (!file_exists($result)) {
    		@mkdir($result, 0770, true);
    	}
    	return $result;
    }

    public function getLinkPurchasedCollection()
    {
    	if (is_null($this->_purchased)&& !is_null($this->_customer)) {
	        $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
	            ->addFieldToFilter('customer_id', $this->_customer->getId())
	            ->addOrder('created_at', 'desc');

	    	$this->_purchased = $purchased;
    	}
    	return $this->_purchased;
    }

    /*
     * Returns a collection of purchased downloadable links and their logged usage
     */
    public function getLinkPurchasedItemCollection()
    {
    	if (is_null($this->_items)) {
	        $purchased = $this->getLinkPurchasedCollection();

	        $purchasedIds = array();
	        foreach ($purchased as $_item) {
	            $purchasedIds[] = $_item->getId();
	        }
	        $purchasedItems = Mage::getResourceModel('downloadable/link_purchased_item_collection')
	            ->addFieldToFilter('purchased_id', array('in' => $purchasedIds))
	            ->setOrder('item_id', 'desc');

	        // Add timestamp of last download to each item
	        foreach ($purchasedItems as $_item) {
	        	$data = Mage::getModel('downloadplus/log')
	        				->getResource()
	        				->addPurchasedLinkToFilter($_item)
	        				->getNewestDownloadTimestamp();

	        	$_item->setData('newest_download_timestamp', $data);
	        }

	        $this->_items = $purchasedItems;
    	}

    	return $this->_items;
    }

    /*
     * Sends a Transactional Email
     */
    public function notifyCustomer($download)
    {
    	$customer = $download->getCustomer();
		$order = $download->getOrder();
		$success = false;
		$config = Mage::getModel('downloadplus/config');

		if ($customer->getEmail()) {
			$mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->setDesignConfig(Array('area' => 'frontend'))
				->sendTransactional(
					$config->getDownloadableDeliveryEmailTemplate(),
					$config->getDownloadableDeliveryEmailIdentity(),
					$customer->getEmail(),
					$customer->getName(),
					Array(
							'customer'		=> $customer,
	                        'order'         => $order,
							'orderitem'		=> $download->getOrderItem(),
	                        'billing'       => $order->getBillingAddress(),
							'linkpurchased' => $download->getLinkPurchased(),
							'linkpurchaseditem' => $download->getLinkPurchasedItem(),
	                        'download'		=> $download
					)
				);

			$success = $mailTemplate->getSentSuccess();
		}

    	return $success;
    }

}