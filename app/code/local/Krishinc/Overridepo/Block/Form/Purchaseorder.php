<?php
class Krishinc_Overridepo_Block_Form_Purchaseorder extends Mage_Payment_Block_Form_Purchaseorder 
{
	 protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('overridepo/form/purchaseorder.phtml');
    }
}