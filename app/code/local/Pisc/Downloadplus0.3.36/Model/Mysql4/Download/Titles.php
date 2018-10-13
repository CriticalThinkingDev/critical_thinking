<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Product Downloads resource model
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author
 */

class Pisc_Downloadplus_Model_Mysql4_Download_Titles extends Mage_Core_Model_Mysql4_Abstract
{

	protected $_product_id = null;

    /**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
        $this->_init('downloadable/link_title', 'title_id');
    }

    public function addProductToFilter($product)
    {
    	if (empty($product)) {
    		$this->_product_id = null;
    	} else {
    		if ($product instanceof Mage_Catalog_Model_Product) {
    			$this->_product_id = $product->getId();
    		} else {
    			$this->_product_id = $product;
    		}
        }

    	return $this;
    }

    /*
     * Returns array with all Link Titles of downloads
     */
    public function getAllLinkTitles()
    {
    	$sql = $this->_getReadAdapter()
		    		->select()
		            ->from(array('main_table' => $this->getTable('downloadable/link')))
		            ->joinLeft(array('link_title_table' => $this->getTable('downloadable/link_title')),
		                '`link_title_table`.link_id=`main_table`.link_id',
		                array('title' => 'title'))
		            ->order('title ASC');

        if (!is_null($this->_product_id)) {
        	$sql = $sql->where('product_id='.$this->_product_id);
        }

        $result = $this->_getReadAdapter()->fetchAll($sql);

        return $result;
    }

    /*
     * Returns array with all Sample Titles of downloads
     */
    public function getAllSampleTitles()
    {
    	$sql = $this->_getReadAdapter()
		    		->select()
		            ->from(array('main_table' => $this->getTable('downloadable/sample')))
		            ->joinLeft(array('sample_title_table' => $this->getTable('downloadable/sample_title')),
		                '`sample_title_table`.sample_id=`main_table`.sample_id',
		                array('title' => 'title'))
		            ->order('title ASC');

        if (!is_null($this->_product_id)) {
        	$sql = $sql->where('product_id='.$this->_product_id);
        }

        $result = $this->_getReadAdapter()->fetchAll($sql);

        return $result;
    }

}
