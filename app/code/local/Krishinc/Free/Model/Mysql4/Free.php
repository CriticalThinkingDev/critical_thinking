<?php

class Krishinc_Free_Model_Mysql4_Free extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the free_id refers to the key field in your database table.
        $this->_init('free/free', 'donation_id');
    }
}