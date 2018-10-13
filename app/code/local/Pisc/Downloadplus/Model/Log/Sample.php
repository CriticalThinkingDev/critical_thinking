<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Samples Log model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Log_Sample extends Pisc_Downloadplus_Model_Log
{
	
	protected $_eventPrefix = 'downloadplus_log_sample';
	
    /*
     * Sets the logging data from Sample Object
     */
    public function setSample($sample) {
    	$this->setLinkId(null);
    	$this->setItemId(null);
    	$this->setSampleId($sample->getId());
    	
    	$this->setIp(Mage::helper('core/http')->getRemoteAddr());
		$this->setTimestamp(date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp()));
    	$this->setStoreId(Mage::app()->getStore()->getId());
    	
    	return $this;
    }

    /*
     * Tracks the download of the sample
     */
    public function trackSample($sample) {
    	$this->setSample($sample);
    	$this->save();
    }

}