<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloadable Sample model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Sample extends Mage_Downloadable_Model_Sample
{

	protected $_eventPrefix = 'downloadplus_sample';
	
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /*
     * Returns a Id by Filename
     */
    public function getIdByFile($file)
    {
    	$result = null;

    	if (!empty($file)) {
	        $sql = $this->_getResource()->getReadConnection()->select()
		            	->from($this->_getResource()->getTable('downloadable/sample'))
		            	->where("sample_file='".$file."'");

	        if ($result = $this->_getResource()->getReadConnection()->fetchOne($sql)) {
	        	return $result;
	        }
    	}

        return null;
    }

    /*
     * Returns the Sample Title
    */
    public function getSampleTitle()
    {
    	$result = null;
    
    	$sql = $this->_getResource()->getReadConnection()->select()
		    	->from($this->_getResource()->getTable('downloadable/sample_title'))
		    	->where('sample_id = ?', $this->getSampleId())
		    	->where('store_id = ?', $this->getStoreId());
    
    	if ($result = $this->_getResource()->getReadConnection()->fetchRow($sql)) {
    		return $result['title'];
    	}
    
    	return null;
    }
    
}
