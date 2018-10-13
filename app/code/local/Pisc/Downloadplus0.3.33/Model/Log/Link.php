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
 * @version		0.1.1
 */
class Pisc_Downloadplus_Model_Log_Link extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_log_link';
	
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
     * Sets the logging data from Sample Object
     */
    public function setPurchasedLink($link) {
    	$this->setLinkId(null);
    	$this->setItemId($link->getId());
    	$this->setIp(isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:null);
    	$this->setTimestamp(Mage::getModel('core/date')->timestamp());
    	$this->setStoreId(Mage::app()->getStore()->getId());
    }

    /*
     * Tracks the download of the sample
     */
    public function trackPurchasedLink($link) {
    	$this->setPurchasedLink($link);
    	$this->save();
    }

}