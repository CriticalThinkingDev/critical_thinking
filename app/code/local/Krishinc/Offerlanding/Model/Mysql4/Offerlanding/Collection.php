<?php

class Krishinc_Offerlanding_Model_Mysql4_Offerlanding_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('offerlanding/offerlanding');
    }
}