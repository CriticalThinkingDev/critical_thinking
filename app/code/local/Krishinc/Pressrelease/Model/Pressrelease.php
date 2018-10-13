<?php

class Krishinc_Pressrelease_Model_Pressrelease extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pressrelease/pressrelease');
    }
    
     public function CheckUrlKey($urlKey){
      
    	$collection = $this->getCollection();
    	$collection->addFieldToFilter('pagelink',$urlKey);
    	
    	if($collection->count() > 0){
    		$brandObj =  $collection->getFirstItem();
    		return $brandObj->getId();
    	}
    	return null;
    }
}