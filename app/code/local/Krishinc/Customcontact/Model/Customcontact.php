<?php

class Krishinc_Customcontact_Model_Customcontact extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customcontact/customcontact');
    }
    
    
     public function isEmailExists($email)
    {
    	$data = $this->getCollection()
    		->addFieldToFilter('email',array('eq',$email))->getFirstItem();
    	if($data)
    	{
    		return $data->getId();
    	} else {
    		return false;
    	}
    }
}