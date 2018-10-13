<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Config Options Feed Channels Groups
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author     PILLWAX Industrial Solutions Consulting
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_System_Config_Source_Feed_Channels
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
    	$options = Array(
    			Array('value'=>'product', 'label'=>Mage::helper('adminhtml')->__('DownloadPlus Extension Updates')),
    			Array('value'=>'magento', 'label'=>Mage::helper('adminhtml')->__('Magento Extension Updates')),
    			Array('value'=>'news', 'label'=>Mage::helper('adminhtml')->__('News on our Magento Extensions')),
    		);

        if(!$isMultiselect){
            array_unshift($options, Array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
}