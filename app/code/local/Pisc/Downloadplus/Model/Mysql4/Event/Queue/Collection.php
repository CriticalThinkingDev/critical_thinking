<?php
/**
 * Downloadplus Event Queue Resource Collection Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Event_Queue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	/**
	 * Initialize connection and define resource
	 */
	protected function  _construct()
	{
		$this->_init('downloadplus/event_queue');
	}
	
}