<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Config Options Serialnumber Email Setting
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author     PILLWAX Industrial Solutions Consulting
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_System_Config_Source_Serialnumber_Email
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
    	Mage::getModel('downloadplus/config');
    	$options = Array(
    			Array('value'=>Pisc_Downloadplus_Model_Config::SERIALNUMBER_EMAIL_SEND_NONE, 'label'=>Mage::helper('downloadplus')->__('Do not send automatically')),
    			Array('value'=>Pisc_Downloadplus_Model_Config::SERIALNUMBER_EMAIL_SEND_ALWAYS, 'label'=>Mage::helper('downloadplus')->__('Always (Frontend & Backend)')),
    			Array('value'=>Pisc_Downloadplus_Model_Config::SERIALNUMBER_EMAIL_SEND_FRONTEND, 'label'=>Mage::helper('downloadplus')->__('Only on Frontend')),
    	);

        if(!$isMultiselect){
            array_unshift($options, Array('value'=>'', 'label'=> Mage::helper('downloadplus')->__('--Please Select--')));
        }

        return $options;
    }
}
