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
 * @author
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
     * Returns the Purchased Link associated with the download
     */
    public function getPurchasedLink() {
    	if ($this->isPurchasedLink()) {
    		return Mage::getModel('downloadable/link_purchased')->load($this->getPurchasedId());
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
    public function isPurchsedLink() {
    	return !is_null($this->getPurchasedId());
    }

    /*
     * Returns if the log entry is a tracked sample
     */
    public function isSample() {
    	return !is_null($this->getSampleId());
    }

    /*
     * Returns the total downloads
     */
    public function getDownloadTotal() {
    	return ($this->getResource()->getDownloadTotal());
    }

}