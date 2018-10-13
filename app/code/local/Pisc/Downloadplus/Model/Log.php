<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Log model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @version		0.1.2
 */
class Pisc_Downloadplus_Model_Log extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_log';
	
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('downloadplus/log');
        parent::_construct();
    }

    /*
     * Tracks an object
     */
    public function track($resourceObject)
    {
		$config = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore());
		
    	if ($config->isDownloadableTrackProduct() && ($resourceObject instanceof Mage_Downloadable_Model_Link_Purchased_Item)) {
    		if ( $resourceObject->getId() ) {
    			$log = Mage::getModel('downloadplus/log_link');
    			$log->trackPurchasedLink($resourceObject);
    		}
    	}
    	if ($config->isDownloadableTrackProduct() && ($resourceObject instanceof Mage_Downloadable_Model_Link)) {
    		if ( $resourceObject->getId() ) {
    			$log = Mage::getModel('downloadplus/log_link');
    			$log->trackLinkSample($resourceObject);
    		}
    	}
    	if ($config->isDownloadableTrackSample() && ($resourceObject instanceof Mage_Downloadable_Model_Sample)) {
    		$log = Mage::getModel('downloadplus/log_sample');
    		$log->trackSample($resourceObject);
    	}
    	if ($config->isDownloadableTrackProduct() && ($resourceObject instanceof Pisc_Downloadplus_Model_Link_Product_Item)) {
    		$log = Mage::getModel('downloadplus/log_product_item');
    		$log->trackLink($resourceObject);
    	}
    	if ($config->isDownloadableTrackProduct() && Mage::helper('downloadplus')->existsDownloadplusBonus() && ($resourceObject instanceof Pisc_DownloadplusBonus_Model_Link_Purchased_Bonus_Item)) {
    		$log = Mage::getModel('downloadplusbonus/log_bonus_item');
    		$log->trackLink($resourceObject);
    	}
    	if ($config->isDownloadableTrackProduct() && ($resourceObject instanceof Pisc_Downloadplus_Model_Link_Customer_Item)) {
    		$log = Mage::getModel('downloadplus/log_customer_item');
    		$log->trackLink($resourceObject);
    	}
    	
		return $this;    	
    }
    
    /*
     * Returns the Purchased Link associated with the download
     */
    public function getPurchasedLink() {
    	if ($this->isPurchasedLink()) {
    		return Mage::getModel('downloadable/link_purchased')->load($this->getItemId());
    	}
    	return false;
    }

    /*
     * Returns the Sample associated with the download
     */
    public function getSample() {
    	if ($this->isSample()) {
    		return Mage::getModel('downloadable/sample')->load($this->getSampleId());
    	}
    	return false;
    }

    /*
     * Returns if the log entry is a tracked product
     */
    public function isPurchasedLink() {
    	return !is_null($this->getItemId());
    }

    /*
     * Returns if the log entry is a tracked sample
     */
    public function isSample() {
    	return !is_null($this->getSampleId());
    }

    /*
     * Returns if the log entry is a tracked Link Sample
     */
    public function isLink() {
    	return !is_null($this->getLinkId());
    }
    
    /*
     * Returns the total downloads
     */
    public function getDownloadTotal() {
    	return ($this->getResource()->getDownloadTotal());
    }

    /*
     * Log Customer ID on Save
     */
    public function save()
    {
    	if ($customer = Mage::getSingleton('customer/session')->getCustomer()) {
    		if (is_null($this->getCustomerId()) && $customer->getId()) {
    			$this->setCustomerId($customer->getId());
    		}
    		if ($store = Mage::app()->getStore() && is_null($this->getStoreId())) {
    			if ($store->getId()) {
    				$this->setStoreId($store->getId());
    			}
    		}
    	}
    	return parent::save();
    }
    
}