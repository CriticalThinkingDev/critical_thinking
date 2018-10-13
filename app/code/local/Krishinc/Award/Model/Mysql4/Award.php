<?php
class Krishinc_Award_Model_Mysql4_Award extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('award/award', 'award_id');
        $this->_isPkAutoIncrement = false;
    }
}