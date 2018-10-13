<?php
/**
 * Downloadplus Prerequisites Check
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_System_Config_Prerequisites extends Mage_Core_Model_Config_Data
{
	
	protected function _afterLoad()
	{
		return parent::_afterLoad();
	}
	
	protected function _beforeSave()
	{
		return $this;
	}	
	
	public function save()
	{
		/* Nothing to save */
		return $this;
	}

	public function create()
	{
		/* Nothing to create */
		return $this;
	}
	
}