<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable links extension resource collection
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Title_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_filter = Array();

    protected function _construct()
    {
    	$this->_init('downloadable/link_title', 'title_id');
    }

    public function addStoreToFilter($store)
    {
    	if (is_object($store)) {
    		$this->_filter[] = 'store_id='.$store->getId();
    	} else {
    		$this->_filter[] = 'store_id='.$store;
    	}
    	 
    	return $this;
    }
    
    public function getUniqueTitles()
    {
    	$sql = $this->getSelect()
    				->reset()
    				->distinct()
    				->from(array('main_table'=>$this->getTable('downloadable/link_title')), array('title'));
    
    	if (!empty($this->_filter)) {
    		$sql->where(implode(' AND ', $this->_filter));
    	}
    	
    	$collection = $this->getResource()->getReadConnection()->fetchAll($sql);
    	return $collection;
    }
    
}