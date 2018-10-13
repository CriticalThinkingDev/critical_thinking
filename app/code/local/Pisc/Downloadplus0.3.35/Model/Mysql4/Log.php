<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Product  Log resource model
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author
 */
class Pisc_Downloadplus_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{

	protected $_filter = Array();

    /**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
        $this->_init('downloadplus/log', 'log_id');
    }

    /*
     * Clears Filter
     */
    public function clearFilter()
    {
    	$this->_filter = Array();
    	return $this;
    }

    /*
     * Adds Link to Filter
     */
    public function addPurchasedLinkToFilter($link)
    {
    	$this->_filter[] = 'item_id='.$link->getItemId();
    	return $this;
    }

    /*
     * Adds Sample to Filter
     */
    public function addSampleToFilter($sample)
    {
    	$this->_filter[] = 'sample_id='.$sample->getSampleId();
    	return $this;
    }

    /*
     * Returns array with total of downloads
     */
    public function getDownloadTotal()
    {
        $sql = $this->_getReadAdapter()->select()
            ->from($this->getTable('downloadplus/log'),
            		array('total' => new Zend_Db_Expr('count(log_id)')));

        if (!empty($this->_filter)) {
        	$sql = $sql->where(implode(' AND ', $this->_filter));
        }

        $result = $this->_getReadAdapter()->fetchOne($sql);

        return $result;
    }

    /*
     * Returns timestamp of oldest download
     */
    public function getOldestDownloadTimestamp()
    {
        $sql = $this->_getReadAdapter()->select()
            ->from($this->getTable('downloadplus/log'),
            		array('oldest' => new Zend_Db_Expr('min(timestamp)')));

        if (!empty($this->_filter)) {
         	$sql = $sql->where(implode(' AND ', $this->_filter));
        }

        $result = $this->_getReadAdapter()->fetchOne($sql);

        return $result;
    }

    /*
     * Returns timestamp of newest download
     */
    public function getNewestDownloadTimestamp()
    {
        $sql = $this->_getReadAdapter()->select()
            ->from($this->getTable('downloadplus/log'),
            		array('newest' => new Zend_Db_Expr('max(timestamp)')));

        if (!empty($this->_filter)) {
        	$sql = $sql->where(implode(' AND ', $this->_filter));
        }

        $result = $this->_getReadAdapter()->fetchOne($sql);

        return $result;
    }

    /*
     * Returns a count of logged downloads for this purchased link
     */
    public function getDownloadCount()
    {
    	$result = 0;
    	$item_id = $link->getItemId();

        $sql = $this->_getReadAdapter()->select()
	            ->from($this->getTable('downloadplus/log'), array('count' => new Zend_Db_Expr('count(log_id)')));

        if (!empty($this->_filter)) {
        	$sql = $sql->where(implode(' AND ', $this->_filter));
        }

        $result = $this->_getReadAdapter()->fetchOne($sql);

    	return $result;
    }

}
