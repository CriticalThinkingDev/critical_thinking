<?php

class Krishinc_Offerlanding_Model_Mysql4_Offerlanding extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the offerlanding_id refers to the key field in your database table.
        $this->_init('offerlanding/offerlanding', 'offerlanding_id');
    }
}