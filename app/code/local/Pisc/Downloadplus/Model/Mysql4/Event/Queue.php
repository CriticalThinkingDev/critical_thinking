<?php
/**
 * Downloadplus Event Queue Resource Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Event_Queue extends Mage_Core_Model_Mysql4_Abstract
{

	protected function _construct()
	{
		$this->_init('downloadplus/event_queue', 'id');
	}
	
}