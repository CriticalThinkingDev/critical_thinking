<?php

class Krishinc_Hardcoder_Model_Hardcoder extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('hardcoder/hardcoder');
    }
}