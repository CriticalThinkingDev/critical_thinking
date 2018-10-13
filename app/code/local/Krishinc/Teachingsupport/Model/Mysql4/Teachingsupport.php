<?php

class Krishinc_Teachingsupport_Model_Mysql4_Teachingsupport extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the teachingsupport_id refers to the key field in your database table.
        $this->_init('teachingsupport/teachingsupport', 'teachingsupport_id');
    }
}