<?php
/**
 * Downloadplus Sample Resource Collection Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusMagazine
 * @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.3
 */

class Pisc_Downloadplus_Model_Mysql4_Sample_Extension_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
    	$this->_init('downloadplus/sample_extension');
    }

}