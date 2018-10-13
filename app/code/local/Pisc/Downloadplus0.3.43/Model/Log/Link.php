<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Link Log model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Log_Link extends Pisc_Downloadplus_Model_Log
{
	
	protected $_eventPrefix = 'downloadplus_log_link';

	public function setPurchasedLink($link)
	{
		$this->setLinkId(null);
		$this->setSampleId(null);
		$this->setItemId($link->getId());
		
		$this->setIp(Mage::helper('core/http')->getRemoteAddr());
		$this->setTimestamp(date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp()));
		$this->setStoreId(Mage::app()->getStore()->getId());
		
		return $this;
	}

	public function setLinkSample($link)
	{
		$this->setLinkId($link->getId());
		$this->setSampleId(null);
		$this->setItemId(null);
		
		$this->setIp(Mage::helper('core/http')->getRemoteAddr());
		$this->setTimestamp(date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp()));
		$this->setStoreId(Mage::app()->getStore()->getId());
	
		return $this;
	}
	
    /*
     * Tracks the download of the purchased link
     */
    public function trackPurchasedLink($link) {
    	$this->setPurchasedLink($link);
    	$this->save();
    }

    /*
     * Tracks the download of the link sample
    */
    public function trackLinkSample($link) {
    	$this->setLinkSample($link);
    	$this->save();
    }
    
}