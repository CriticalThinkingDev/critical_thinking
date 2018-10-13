<?php
/**
 * Downloadplus Link Purchased Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.1
 */

class Pisc_Downloadplus_Model_Link_Purchased extends Mage_Downloadable_Model_Link_Purchased
{

	protected function _construct()
	{
		parent::_construct();
		$this->_init('downloadplus/link_purchased');
	}

	public function _beforeSave()
	{
	    if (is_null($this->getOrderId())) {
	        throw new Exception(
	            Mage::helper('downloadable')->__('Order id cannot be null'));
	    }

	    if (method_exists($this, 'isObjectNew') && !$this->getId()) {
	        $this->isObjectNew(true);
	    }
	    Mage::dispatchEvent('model_save_before', array('object'=>$this));
	    Mage::dispatchEvent($this->_eventPrefix.'_save_before', $this->_getEventData());
	    return $this;
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
