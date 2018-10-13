<?php

class Krishinc_Sourcecode_Block_Onepage_Sourcecode extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {    	
        $this->getCheckout()->setStepData('sourcecode', array(
            'label'     => Mage::helper('checkout')->__('Source Code'),
            'is_show'   => true
        ));
        
        parent::_construct();
    }
}