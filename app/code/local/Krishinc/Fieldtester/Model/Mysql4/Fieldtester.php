<?php

class Krishinc_Fieldtester_Model_Mysql4_Fieldtester extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the fieldtester_id refers to the key field in your database table.
        $this->_init('fieldtester/fieldtester', 'fieldtester_id');
    }
}