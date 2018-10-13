<?php

class Krishinc_Fieldtester_Model_Fieldtester extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('fieldtester/fieldtester');
    }
   
}