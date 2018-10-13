<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Adminnotification Options
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author     PILLWAX Industrial Solutions Consulting
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_System_Config_Source_Adminnotification_Type
{

    const SEVERITY_CRITICAL = 1;
    const SEVERITY_MAJOR    = 2;
    const SEVERITY_MINOR    = 3;
    const SEVERITY_NOTICE   = 4;

    public function toOptionArray()
    {
        return array(
            array('value'=>self::SEVERITY_NOTICE, 'label'=>Mage::helper('downloadplus')->__('Notice')),
            array('value'=>self::SEVERITY_MINOR, 'label'=>Mage::helper('downloadplus')->__('Minor')),
            array('value'=>self::SEVERITY_MAJOR, 'label'=>Mage::helper('downloadplus')->__('Major')),
            array('value'=>self::SEVERITY_CRITICAL, 'label'=>Mage::helper('downloadplus')->__('Critical')),
        );
    }

}
