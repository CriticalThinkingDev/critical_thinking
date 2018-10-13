<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license    Commercial Unlimited License
 * @version    0.1.1
 */

class Pisc_Downloadplus_Model_System_Config_Source_Behaviour
{

    public function toOptionArray()
    {
    	// Load dependency
    	Mage::getModel('downloadplus/config');
    	
        return array(
            array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_MAGENTO, 'label'=>Mage::helper('downloadplus')->__('Magento Behaviour - Always deliver purchased file')),
            array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_LATEST, 'label'=>Mage::helper('downloadplus')->__('Deliver most recent purchased file')),
        );
    }

}
