<?php

class Krishinc_Educent_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('educent')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('educent')->__('Disabled')
        );
    }
}