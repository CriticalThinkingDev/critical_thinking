<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license    Commercial Unlimited License
 * @version    0.1.1
 */

class Pisc_Downloadplus_Model_System_Config_Source_Filenamemixedcase
{

    public function toOptionArray()
    {
    	// Load dependency
    	Mage::getModel('downloadplus/config');
    	
        return array(
            array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_NO, 'label'=>Mage::helper('downloadplus')->__('Magento Behaviour - All filenames lowercase')),
            array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_YES, 'label'=>Mage::helper('downloadplus')->__('Allow mixed case filenames')),
        );
    }

}
