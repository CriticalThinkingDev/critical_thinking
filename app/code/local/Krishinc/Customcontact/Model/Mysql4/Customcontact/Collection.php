<?php

class Krishinc_Customcontact_Model_Mysql4_Customcontact_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customcontact/customcontact');
    }
}