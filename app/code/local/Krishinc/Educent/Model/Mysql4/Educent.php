<?php

class Krishinc_Educent_Model_Mysql4_Educent extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the educent_id refers to the key field in your database table.
        $this->_init('educent/educent', 'educent_id');
    }
}