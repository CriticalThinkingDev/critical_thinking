<?php

class Krishinc_Free_Model_Mysql4_Free_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('free/free');
    }
}