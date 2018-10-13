<?php

class Krishinc_Pressrelease_Model_Mysql4_Pressrelease_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pressrelease/pressrelease');
    }
}