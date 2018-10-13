<?php
/**
 * Downloadplus Link Resource Collection
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Collection extends Mage_Downloadable_Model_Mysql4_Link_Collection
{

	protected function _construct()
	{
		parent::_construct();
		$this->_init('downloadplus/link');
	}
	
}