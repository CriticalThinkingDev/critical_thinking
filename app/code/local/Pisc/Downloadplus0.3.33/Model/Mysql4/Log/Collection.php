<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Product  Log Collection resource model
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author
 */
class Pisc_Downloadplus_Model_Mysql4_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

	protected $_filter = Array();

    /**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
        parent::_construct();
    	$this->_init('downloadplus/log', 'log_id');
    }

    public function clearFilter()
    {
    	$this->_filter = Array();

    	return $this;
    }

    public function addStoreToFilter($store)
    {
    	if (empty($store)) {
    		$this->_filter[] = 'main_table.store_id IS NULL';
    	} elseif ($store instanceof Mage_Core_Model_Store) {
    		$this->_filter[] = 'main_table.store_id="'.$store->getId().'"';
    	} else {
    		$this->_filter[] = 'main_table.store_id="'.$store.'"';
    	}

    	return $this;
    }

    public function addDetailsToResult()
    {
        $this->getSelect()
            ->joinLeft(array('sample_title_table' => $this->getTable('downloadable/sample_title')),
                '`sample_title_table`.sample_id=`main_table`.sample_id',
                array('title' => 'title'))
            ->joinLeft(array('sample_table' => $this->getTable('downloadable/sample')),
                '`sample_table`.sample_id=`main_table`.sample_id',
                array('product_id' => 'product_id', 'product_id' => new Zend_Db_Expr('IFNULL(`purchased_link_item_table`.product_id, `sample_table`.product_id)')))
            ->joinLeft(array('purchased_link_item_table' => $this->getTable('downloadable/link_purchased_item')),
                '`purchased_link_item_table`.item_id=`main_table`.item_id',
                array('purchased_id' => 'purchased_id', 'title' => 'link_title', 'title' => new Zend_Db_Expr('IFNULL(`purchased_link_item_table`.link_title, `sample_title_table`.title)')))
            ->joinLeft(array('purchased_link_table' => $this->getTable('downloadable/link_purchased')),
                '`purchased_link_table`.purchased_id=`purchased_link_item_table`.purchased_id',
                array('customer_id' => 'customer_id', 'product_sku' => 'product_sku', 'product_name' => 'product_name', 'order_increment_id' => 'order_increment_id'));

            //$sql = $this->getSelect()->__toString();

        return $this;
    }

    public function getTopDownloadProducts()
    {
    	$where = $this->_filter;
    	$where[] = 'NOT ISNULL(main_table.item_id)';

    	$this->getSelect()
    		->reset()
            ->from(array('main_table' => $this->getTable('downloadplus/log')),
            	array('total' => new Zend_Db_Expr('count(log_id)')))
            ->where(implode(' AND ', $where))
            ->joinLeft(array('purchased_link_item_table' => $this->getTable('downloadable/link_purchased_item')),
                '`purchased_link_item_table`.item_id=`main_table`.item_id',
                array('title' => 'link_title'))
            ->joinLeft(array('purchased_link_table' => $this->getTable('downloadable/link_purchased')),
                '`purchased_link_table`.purchased_id=`purchased_link_item_table`.purchased_id',
                array('product_sku' => 'product_sku', 'product_name' => 'product_name'))
            ->group('purchased_link_item_table.link_id')
            ->order('total DESC');

            //$sql = $this->getSelect()->__toString();

         return $this;
    }

    public function getTopDownloadSamples()
    {
    	$where = $this->_filter;
    	$where[]='NOT ISNULL(main_table.sample_id)';

    	$this->getSelect()
    		->reset()
            ->from(array('main_table' => $this->getTable('downloadplus/log')),
            	array('sample_id' => 'sample_id', 'product_id' => null, 'total' => new Zend_Db_Expr('count(log_id)')))
            ->where(implode(' AND ', $where))
            ->group('main_table.sample_id')
            ->order('total DESC')
            ->joinLeft(array('sample_title_table' => $this->getTable('downloadable/sample_title')),
                '`sample_title_table`.sample_id=`main_table`.sample_id',
                array('title' => 'title'))
            ->joinLeft(array('sample_table' => $this->getTable('downloadable/sample')),
                '`sample_table`.sample_id=`main_table`.sample_id',
                array('product_id' => 'product_id', 'product_id' => new Zend_Db_Expr('IFNULL(`purchased_link_item_table`.product_id, `sample_table`.product_id)')))
            ->joinLeft(array('purchased_link_item_table' => $this->getTable('downloadable/link_purchased_item')),
                '`purchased_link_item_table`.item_id=`main_table`.item_id',
                array('title' => 'link_title', 'title' => new Zend_Db_Expr('IFNULL(`purchased_link_item_table`.link_title, `sample_title_table`.title)')))
            ->joinLeft(array('purchased_link_table' => $this->getTable('downloadable/link_purchased')),
                '`purchased_link_table`.purchased_id=`purchased_link_item_table`.purchased_id',
                array('customer_id' => 'customer_id', 'product_sku' => 'product_sku', 'product_name' => 'product_name'));

            //$sql = $this->getSelect()->__toString();

    	return $this;
    }

}
