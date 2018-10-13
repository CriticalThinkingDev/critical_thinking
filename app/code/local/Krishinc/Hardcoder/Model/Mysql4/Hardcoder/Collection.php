<?php

class Krishinc_Hardcoder_Model_Mysql4_Hardcoder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('hardcoder/hardcoder');
    }
}