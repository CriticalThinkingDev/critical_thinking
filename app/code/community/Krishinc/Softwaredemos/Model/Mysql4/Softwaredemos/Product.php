<?php

class Krishinc_softwaredemos_Model_Mysql4_softwaredemos_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the softwaredemos_product_id refers to the key field in your database table.
         $this->_init('softwaredemos/softwaredemos_product','softwaredemos_product_id'); 
    }
    
 
}