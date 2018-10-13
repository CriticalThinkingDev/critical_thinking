<?php
/**
 * Downloadplus Sample Extension Resource Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusMagazine
 * @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.3
 */

class Pisc_Downloadplus_Model_Mysql4_Sample_Extension extends Mage_Core_Model_Mysql4_Abstract
{

	protected $_filter = Array();
	
    protected function  _construct()
    {
        $this->_init('downloadplus/sample_extension', 'id');
    }

    /*
     * Returns the resource ID by LinkId
     */
    public function getIdBySampleId($id)
    {
    	$result = null;
    	if ($id) {
	    	$where = $this->_filter;
	    	$where[] = "sample_id=".$id;

	        $sql = $this->_getReadAdapter()->select()
	        			->from($this->getTable('downloadplus/sample_extension'))
	        			->where(implode(' AND ', $where));

	        $result = $this->_getReadAdapter()->fetchOne($sql);
    	}

        return $result;
    }

    public function addSampleIdsToFilter($sampleIds)
    {
    	$this->_filter[] = 'sample_id IN ('.implode(',', $sampleIds).')';
    	return $this;
    }
    
}
