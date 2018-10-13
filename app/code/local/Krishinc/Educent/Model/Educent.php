<?php

class Krishinc_Educent_Model_Educent extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('educent/educent');
    }
}