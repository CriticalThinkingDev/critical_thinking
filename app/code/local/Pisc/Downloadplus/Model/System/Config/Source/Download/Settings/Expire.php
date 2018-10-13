<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Config Options Download Expire Setting
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author     PILLWAX Industrial Solutions Consulting
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_System_Config_Source_Download_Settings_Expire
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
    	// Load dependency
    	Mage::getModel('downloadplus/link_extension');
    	
    	$options = Array(
    			Array('value'=>Pisc_Downloadplus_Model_Link_Extension::EXPIRE_ON_NEVER, 'label'=>Mage::helper('downloadplus')->__('Never expire')),
    			Array('value'=>Pisc_Downloadplus_Model_Link_Extension::EXPIRE_ON_ORDER, 'label'=>Mage::helper('downloadplus')->__('Start expiry on order')),
    			Array('value'=>Pisc_Downloadplus_Model_Link_Extension::EXPIRE_ON_DOWNLOAD, 'label'=>Mage::helper('downloadplus')->__('Start expiry after first download')),
    		);

        if(!$isMultiselect){
            array_unshift($options, Array('value'=>'', 'label'=> Mage::helper('downloadplus')->__('-- Please Select --')));
        }

        return $options;
    }
}