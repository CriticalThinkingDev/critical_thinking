<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable link purchased item model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.5
 */

class Pisc_Downloadplus_Model_Link_Purchased_Item extends Mage_Downloadable_Model_Link_Purchased_Item
{
	
	protected $_eventPrefix = 'downloadplus_link_purchased_item';

	protected function _construct()
    {
        parent::_construct();
        $this->_init('downloadplus/link_purchased_item');
    }

    public function getLink()
    {
    	if ($this->getLinkId()) {
    		return Mage::getModel('downloadplus/link')->load($this->getLinkId());
    	}
    	return null;
    }
    
    public function getExtension()
    {
    	return Mage::getModel('downloadplus/link_purchased_item_extension')->loadByPurchasedLink($this);
    }

    public function getLinkPurchased()
    {
    	$result = Mage::getModel('downloadable/link_purchased');
    	if ($this->getPurchasedId()) {
   			$result->load($this->getPurchasedId());
    	}
    	return $result;
    }
    
    public function getOrder()
    {
    	$result = Mage::getModel('sales/order');
    	if ($this->getPurchasedId()) {
    		$result->load($this->getLinkPurchased()->getOrderId());
    	}
    	return $result;
    }
    
    public function getOrderItem()
    {
    	$result = Mage::getModel('sales/order_item');
    	if ($this->getOrderItemId()) {
    		$result->load($this->getOrderItemId());
    	}
    	return $result;
    }

    public function getStatus()
    {
		$extension = $this->getExtension();
		if ($extension->getId() && $extension->isExpired()) {
			return self::LINK_STATUS_EXPIRED;
		}
		return parent::getStatus();
    }
    
    /*
     * Sets Resource Model flag to disable Foreign Key Checks on Save;
     */
    public function disableForeignKeyCheck()
    {
    	$this->getResource()->disableForeignKeyCheck();
    	return $this;
    }
    
}
