<?php
class Krishinc_Award_Model_Mysql4_Award_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('award/award');
    }
}