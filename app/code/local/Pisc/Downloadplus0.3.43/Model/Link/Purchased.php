<?php
/**
 * Downloadplus Link Purchased Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Link_Purchased extends Mage_Downloadable_Model_Link_Purchased
{

	protected function _construct()
	{
		parent::_construct();
		$this->_init('downloadplus/link_purchased');
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
