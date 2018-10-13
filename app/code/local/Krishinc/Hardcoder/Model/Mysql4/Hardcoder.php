<?php

class Krishinc_Hardcoder_Model_Mysql4_Hardcoder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the hardcoder_id refers to the key field in your database table.
        $this->_init('hardcoder/hardcoder', 'hardcoder_id');
    }
}