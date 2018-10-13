<?php

class Krishinc_Standards_Model_Mysql4_Standards extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the standards_id refers to the key field in your database table.
        $this->_init('standards/standards', 'standards_id');
    }
}