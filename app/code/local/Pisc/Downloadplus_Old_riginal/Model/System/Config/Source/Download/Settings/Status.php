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

class Pisc_Downloadplus_Model_System_Config_Source_Download_Settings_Status
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
    	// Load dependency
    	Mage::getModel('downloadable/link_purchased_item');
    	Mage::getModel('downloadplus/config');
    	
    	$options = Array(
    			Array('value'=>Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING, 'label'=>Mage::helper('downloadplus')->__('pending')),
    			Array('value'=>Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT, 'label'=>Mage::helper('downloadplus')->__('pending payment')),
    			Array('value'=>Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE, 'label'=>Mage::helper('downloadplus')->__('available')),
    			Array('value'=>Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED, 'label'=>Mage::helper('downloadplus')->__('expired')),
    			Array('value'=>Pisc_Downloadplus_Model_Config::LINK_STATUS_REFRESH, 'label'=>Mage::helper('downloadplus')->__('refresh')),
    			Array('value'=>Pisc_Downloadplus_Model_Config::LINK_STATUS_UPDATE, 'label'=>Mage::helper('downloadplus')->__('update')),
    		);

        if(!$isMultiselect){
            array_unshift($options, Array('value'=>'', 'label'=> Mage::helper('downloadplus')->__('-- Please Select --')));
        }

        return $options;
    }
    
    public function toOptions($isMultiselect=false)
    {
    	$options = Array();
    	$optArray = $this->toOptionArray($isMultiselect);
    	foreach ($optArray as $option) {
    		$options[$option['value']] = $option['label'];
    	}
    	return $options;
    }
    
}