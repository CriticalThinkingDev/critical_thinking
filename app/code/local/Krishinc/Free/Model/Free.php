<?php

class Krishinc_Free_Model_Free extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('free/free');
    }
}