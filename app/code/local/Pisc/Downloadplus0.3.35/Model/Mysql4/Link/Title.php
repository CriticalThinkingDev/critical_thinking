<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable link title resource
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Link_Title extends Mage_Core_Model_Mysql4_Abstract
{

    protected function  _construct()
    {
        $this->_init('downloadplus/link_title', 'title_id');
    }

    public function saveTitle($linkObject)
    {
	    $sql = $this->_getReadAdapter()->select()
	    			->from($this->getTable('downloadable/link_title'))
			    	->where('link_id = ?', $linkObject->getId())
			    	->where('store_id = ?', $linkObject->getStoreId());
	    
	    if ($this->_getReadAdapter()->fetchOne($sql)) {
	    	$where = $this->_getReadAdapter()->quoteInto('link_id = ?', $linkObject->getId()) .
	                    ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $linkObject->getStoreId());
	    	
	    	if ($linkObject->getUseDefaultTitle()) {
	    		$this->_getWriteAdapter()->delete(
	    					$this->getTable('downloadable/link_title'), $where);
	    	} else {
	    		$this->_getWriteAdapter()->update(
	    					$this->getTable('downloadable/link_title'),
	    					array('title' => $linkObject->getTitle()), $where);
	    	}
	    } else {
	    	if (!$linkObject->getUseDefaultTitle()) {
	    		$this->_getWriteAdapter()->insert(
	    		$this->getTable('downloadable/link_title'),
	    				array(
                      		'link_id' => $linkObject->getId(),
                            'store_id' => $linkObject->getStoreId(),
                            'title' => $linkObject->getTitle(),
	    				));
	    	}
	    }
    }
    
}
