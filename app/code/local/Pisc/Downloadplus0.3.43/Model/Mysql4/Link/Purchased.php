<?php
/**
 * Downloadplus Link Purchased Resource Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Purchased extends Mage_Downloadable_Model_Mysql4_Link_Purchased
{
	protected $_FK_CHECK_DISABLED = false;
	
	public function disableForeignKeyCheck()
	{
		$this->_FK_CHECK_DISABLED = true;
	}
	
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if ($this->_FK_CHECK_DISABLED) {
			$adapter = $this->_getWriteAdapter();
			$adapter->raw_query('SET foreign_key_checks = 0');
		}
	}
	
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if ($this->_FK_CHECK_DISABLED) {
			$adapter = $this->_getWriteAdapter();
			$adapter->raw_query('SET foreign_key_checks = 1');
		}
		$this->_FK_CHECK_DISABLED = false;
	}
	
}
