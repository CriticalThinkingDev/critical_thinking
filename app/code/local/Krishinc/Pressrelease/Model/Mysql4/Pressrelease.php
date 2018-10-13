<?php

class Krishinc_Pressrelease_Model_Mysql4_Pressrelease extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the pressrelease_id refers to the key field in your database table.
        $this->_init('pressrelease/pressrelease', 'pressrelease_id');
    }
}