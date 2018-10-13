<?php
/**
 * Downloadplus Link History Resource Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusBonus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_History extends Mage_Core_Model_Mysql4_Abstract
{

	protected function  _construct()
	{
		$this->_init('downloadplus/link_history', 'item_id');
	}
	
}