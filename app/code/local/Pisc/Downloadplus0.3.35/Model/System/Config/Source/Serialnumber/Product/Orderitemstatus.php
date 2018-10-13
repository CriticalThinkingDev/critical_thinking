<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Config Options Serialnumber Product Order Item Status
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author     PILLWAX Industrial Solutions Consulting
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_System_Config_Source_Serialnumber_Product_Orderitemstatus
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_PENDING,
                'label' => Mage::helper('downloadable')->__('Pending')
            ),
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_INVOICED,
                'label' => Mage::helper('downloadable')->__('Invoiced')
            ),
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_SHIPPED,
                'label' => Mage::helper('downloadable')->__('Shipped')
        	),
        	array(
        		'value' => Mage_Sales_Model_Order_Item::STATUS_MIXED,
        		'label' => Mage::helper('downloadable')->__('Mixed')
        	)
        );
    }
}
