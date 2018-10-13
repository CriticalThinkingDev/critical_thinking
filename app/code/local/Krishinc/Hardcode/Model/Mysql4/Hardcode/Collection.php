<?php

class Krishinc_Hardcode_Model_Mysql4_Hardcode_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('hardcode/hardcode');
    }
}