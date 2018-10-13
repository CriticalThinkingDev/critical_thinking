<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Additional Customer Item Link Log model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Log_Customer_Item extends Pisc_Downloadplus_Model_Log
{
	
	protected $_eventPrefix = 'downloadplus_log_customer_item';

	public function setLink($link)
	{
		$this->setLinkId(null);
		$this->setSampleId(null);
		$this->setItemId(null);
		$this->setProductItemId(null);
		$this->setCustomerItemId($link->getId());
		
		$this->setIp(Mage::helper('core/http')->getRemoteAddr());
		$this->setTimestamp(date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp()));
		$this->setStoreId(Mage::app()->getStore()->getId());
		
		return $this;
	}

    /*
     * Tracks the download of the purchased link
     */
    public function trackLink($link) {
    	$this->setLink($link);
    	$this->save();
    }

}