<?php
class Krishinc_Overridepo_Block_Info_Checkmo extends Mage_Payment_Block_Info_Checkmo
{
	 protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('overridepo/info/checkmo.phtml'); 
    }

    public function toPdf()
    {
        $this->setTemplate('overridepo/info/pdf/checkmo.phtml');
        return $this->toHtml();
    } 
}