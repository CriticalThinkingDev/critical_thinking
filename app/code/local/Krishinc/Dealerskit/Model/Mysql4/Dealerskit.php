<?php

class Krishinc_Dealerskit_Model_Mysql4_Dealerskit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the dealerskit_id refers to the key field in your database table.
        $this->_init('dealerskit/dealerskit', 'dealerskit_id');
    }
}