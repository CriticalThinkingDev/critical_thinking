<?php

class Krishinc_Hardcode_Model_Mysql4_Hardcode extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the hardcode_id refers to the key field in your database table.
        $this->_init('hardcode/hardcode', 'hardcode_id');
    }
}