<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable link extension resource
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.2
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Extension extends Mage_Core_Model_Mysql4_Abstract
{

	protected $_filter = Array();
	
    protected function  _construct()
    {
        $this->_init('downloadplus/link_extension', 'id');
    }

    /*
     * Returns the resource ID by LinkId
     */
    public function getIdByLinkId($id)
    {
    	$result = null;
    	if ($id) {
	    	$where = $this->_filter;
	    	$where[] = "link_id=".$id;

	        $sql = $this->_getReadAdapter()->select()
	        			->from($this->getTable('downloadplus/link_extension'))
	        			->where(implode(' AND ', $where));

	        $result = $this->_getReadAdapter()->fetchOne($sql);
    	}

        return $result;
    }

    public function addLinkIdsToFilter($linkIds)
    {
    	$this->_filter[] = 'link_id IN ('.implode(',', $linkIds).')';
    	return $this;
    }
    
    /*
     * Returns selected serialnumber pools
     */
    public function getSerialnumberPools()
    {
    	Mage::getModel('downloadplus/config');
    	$result = null;
    	
    	$where = $this->_filter;
    	$where[] = "serial_number_pool IS NOT NULL";
    	$where[] = "serial_number_pool<>'".Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE."'";
    	 
    	$sql = $this->_getReadAdapter()
    				->select()
    				->distinct()
	        		->from($this->getTable('downloadplus/link_extension'), 'serial_number_pool')
	        		->where(implode(' AND ', $where));
    	
    	$result = $this->_getReadAdapter()->fetchCol($sql);
    	
    	return $result;
    }
    
}
