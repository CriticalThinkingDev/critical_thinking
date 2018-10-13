<?php
class Krishinc_Overridepo_Block_Form_Checkmo extends Mage_Payment_Block_Form_Checkmo 
{
	 protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('overridepo/form/checkmo.phtml');
    }
}