<?php

class Krishinc_Catalogrequest_Model_Mysql4_Catalogrequest_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('catalogrequest/catalogrequest');
    }
}