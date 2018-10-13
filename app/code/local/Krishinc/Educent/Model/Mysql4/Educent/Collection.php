<?php

class Krishinc_Educent_Model_Mysql4_Educent_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('educent/educent');
    }
}