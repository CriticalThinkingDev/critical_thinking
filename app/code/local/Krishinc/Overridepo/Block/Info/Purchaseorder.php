<?php
class Krishinc_Overridepo_Block_Info_Purchaseorder extends Mage_Payment_Block_Info_Purchaseorder
{
	 protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('overridepo/info/purchaseorder.phtml');
    }

    public function toPdf()
    {
        $this->setTemplate('overridepo/info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }
}