<?php
/**
 * Downloadplus Sample Resource Collection Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusMagazine
 * @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Sample_Collection extends Mage_Downloadable_Model_Mysql4_Sample_Collection
{
	
	protected function _construct()
	{
		parent::_construct();
		$this->_init('downloadplus/sample');
	}
	

}