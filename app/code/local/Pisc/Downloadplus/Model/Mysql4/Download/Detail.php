<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Product  Link Log resource model
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @version		0.1.7
 * @author
 */
class Pisc_Downloadplus_Model_Mysql4_Download_Detail extends Mage_Core_Model_Mysql4_Abstract
{

	protected $_filter = Array();

	/**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
    	$this->_init('downloadplus/download_detail', 'detail_id');
    }

    public function getReadAdapter()
    {
    	return $this->_getReadAdapter();
    }

    /*
     * Returns the resource ID by File
     */
    public function getIdByFile($file)
    {
    	$where = $this->_filter;
    	$this->addFileToFilter($file);

    	$sql = $this->_getReadAdapter()->select()
        			->from($this->getTable('downloadplus/download_detail'))
        			->where(implode(' AND ', $this->_filter));

        $result = $this->_getReadAdapter()->fetchOne($sql);

        $this->_filter = $where;

        return $result;
    }

    public function addFileToFilter($file)
    {
    	Mage::helper('downloadplus/download');
    	$helper = Mage::helper('downloadplus/files');
    	if ($helper->isInPath($file, Mage::getModel('downloadplus/link')->getBasePath())) {
    		$this->addPathToFilter(Mage::getModel('downloadplus/link')->getBasePath());
    		$file = $helper->removePath($file, Mage::getModel('downloadplus/link')->getBasePath());
    	}
    	if ($helper->isInPath($file, Mage::getModel('downloadplus/link')->getBaseSamplePath())) {
    		$this->addPathToFilter(Mage::getModel('downloadplus/link')->getBaseSamplePath());
    		$file = $helper->removePath($file, Mage::getModel('downloadplus/link')->getBaseSamplePath());
    	}
    	if ($helper->isInPath($file, Mage::getModel('downloadplus/sample')->getBasePath())) {
    		$this->addPathToFilter(Mage::getModel('downloadplus/sample')->getBasePath());
    		$file = $helper->removePath($file, Mage::getModel('downloadplus/sample')->getBasePath());
    	}
    	if ($helper->isInPath($file, Mage::getModel('downloadplus/link')->getBasePath(Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL))) {
    		$this->addPathToFilter(Mage::getModel('downloadplus/link')->getBasePath(Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL));
    		$file = $helper->removePath($file, Mage::getModel('downloadplus/link')->getBasePath(Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL));
    	}
    	 
    	if ($helper->isInPath($file, Mage::getModel('downloadplus/customer_download')->getBasePath())) {
    		$this->addPathToFilter(Mage::getModel('downloadplus/customer_download')->getBasePath());
    		$file = $helper->removePath($file, Mage::getModel('downloadplus/customer_download')->getBasePath());
    	}
    	if ($helper->isInPath($file, Mage::getModel('downloadplus/product_download')->getBasePath())) {
    		$this->addPathToFilter(Mage::getModel('downloadplus/product_download')->getBasePath());
    		$file = $helper->removePath($file, Mage::getModel('downloadplus/product_download')->getBasePath());
    	}
    	$this->_filter[] = "file='".str_replace(DS, '/', $file)."'";
    	return $this;
    }

    public function addPathToFilter($path)
    {
    	if ($path==Mage::getModel('downloadable/link')->getBasePath()) {
    		$this->_filter[] = 'link_id IS NOT NULL';
    	}
    	if ($path==Mage::getModel('downloadable/link')->getBaseSamplePath()) {
    		$this->_filter[] = 'link_sample_id IS NOT NULL';
    	}
    	if ($path==Mage::getModel('downloadable/sample')->getBasePath()) {
    		$this->_filter[] = 'sample_id IS NOT NULL';
    	}
    	if ($path==Mage::getModel('downloadplus/customer_download')->getBasePath()) {
    		$this->_filter[] = 'link_customer_item_id IS NOT NULL';
    	}
    	if ($path==Mage::getModel('downloadplus/product_download')->getBasePath()) {
    		$this->_filter[] = 'link_product_item_id IS NOT NULL';
    	}
    	return $this;
    }

    public function addStoreToFilter($store)
    {
    	if (is_null($store)) {
    		$this->_filter[] = 'store_id=0';
    	} elseif ($store instanceof Mage_Core_Model_Store) {
    		$this->_filter[] = 'store_id="'.$store->getId().'"';
    	} else {
    		$this->_filter[] = 'store_id="'.$store.'"';
    	}
    	return $this;
    }

    public function clearFilter()
    {
    	$this->_filter = Array();
    	return $this;
    }

    public function addActiveToFilter()
    {
   		$this->_filter[] = 'active=1';
    	return $this;
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
	        			->from($this->getTable('downloadplus/download_detail'))
	        			->where(implode(' AND ', $where));

	        $result = $this->_getReadAdapter()->fetchOne($sql);
    	}

        return $result;
    }

    /*
     * Returns the resource ID by LinkSampleId
     */
    public function getIdByLinkSampleId($id)
    {
    	$result = null;
    	if ($id) {
	    	$where = $this->_filter;
	    	$where[] = "link_sample_id=".$id;

	        $sql = $this->_getReadAdapter()->select()
	        			->from($this->getTable('downloadplus/download_detail'))
	        			->where(implode(' AND ', $where));

	        $result = $this->_getReadAdapter()->fetchOne($sql);
    	}

        return $result;
    }

    /*
     * Returns the resource ID by SampleId
     */
    public function getIdBySampleId($id)
    {
    	$result = null;
    	if ($id) {
	    	$where = $this->_filter;
    		$where[] = "sample_id=".$id;

	        $sql = $this->_getReadAdapter()->select()
	        			->from($this->getTable('downloadplus/download_detail'))
	        			->where(implode(' AND ', $where));

	        $result = $this->_getReadAdapter()->fetchOne($sql);
    	}

        return $result;
    }

    /*
     * Returns the resource ID by LinkCustomerItemId
     */
    public function getIdByLinkCustomerItemId($id)
    {
    	$result = null;
    	if ($id) {
	    	$where = $this->_filter;
    		$where[] = "link_customer_item_id=".$id;

	        $sql = $this->_getReadAdapter()->select()
	        			->from($this->getTable('downloadplus/download_detail'))
	        			->where(implode(' AND ', $where));

	        $result = $this->_getReadAdapter()->fetchOne($sql);
    	}

        return $result;
    }

    /*
     * Returns the resource ID by LinkProductItemId
     */
    public function getIdByLinkProductItemId($id)
    {
    	$result = null;
    	if ($id) {
	    	$where = $this->_filter;
    		$where[] = "link_product_item_id=".$id;

	        $sql = $this->_getReadAdapter()->select()
	        			->from($this->getTable('downloadplus/download_detail'))
	        			->where(implode(' AND ', $where));

	        $result = $this->_getReadAdapter()->fetchOne($sql);
    	}

        return $result;
    }

    /*
     * Returns the resource ID by LinkProductItemId
     */
    public function getIdByLinkBonusItemId($id)
    {
    	$result = null;
    	if ($id) {
    		$where = $this->_filter;
    		$where[] = "link_bonus_item_id=".$id;
    
    		$sql = $this->_getReadAdapter()->select()
    		->from($this->getTable('downloadplus/download_detail'))
    		->where(implode(' AND ', $where));
    
    		$result = $this->_getReadAdapter()->fetchOne($sql);
    	}
    
    	return $result;
    }
    
    /**
     * Returns a Link ID by File
     */
    public function getLinkIdByFile($file)
    {
		$sql = $this->_getReadAdapter()->select()
					->from($this->getTable('downloadable/link'))
					->where("link_file='".str_replace(DS, '/', $file)."'");

		if ($result = $this->_getReadAdapter()->fetchOne($sql)) {
			return $result;
		}

		return null;
    }

    /**
     * Returns a Link ID by Sample File
     */
    public function getLinkIdBySampleFile($file)
    {
		$sql = $this->_getReadAdapter()->select()
					->from($this->getTable('downloadable/link'))
					->where("sample_file='".str_replace(DS, '/', $file)."'");

		if ($result = $this->_getReadAdapter()->fetchOne($sql)) {
			return $result;
		}

		return null;
    }

    /**
     * Returns a Sample ID by File
     */
    public function getSampleIdByFile($file)
    {
		$sql = $this->_getReadAdapter()->select()
					->from($this->getTable('downloadable/sample'))
					->where("sample_file='".str_replace(DS, '/', $file)."'");

		if ($result = $this->_getReadAdapter()->fetchOne($sql)) {
			return $result;
		}

		return null;
    }

    /**
     * Returns a Link Purchased Item ID by File
     */
    public function getLinkPurchasedItemIdByFile($file)
    {
		$sql = $this->_getReadAdapter()->select()
					->from($this->getTable('downloadable/link_purchased_item'))
					->where("link_file='".str_replace(DS, '/', $file)."'");

		if ($result = $this->_getReadAdapter()->fetchOne($sql)) {
			return $result;
		}

		return null;
    }

    /**
     * Returns a Link Customer Item ID by File
     */
    public function getLinkCustomerItemIdByFile($file)
    {
		$sql = $this->_getReadAdapter()->select()
					->from($this->getTable('downloadplus/link_customer_item'))
					->where("link_file='".str_replace(DS, '/', $file)."'");

		if ($result = $this->_getReadAdapter()->fetchOne($sql)) {
			return $result;
		}

		return null;
    }

    /**
     * Returns a Link Product Item ID by File
     */
    public function getLinkProductItemIdByFile($file)
    {
		$sql = $this->_getReadAdapter()->select()
					->from($this->getTable('downloadplus/link_product_item'))
					->where("link_file='".str_replace(DS, '/', $file)."'");

		if ($result = $this->_getReadAdapter()->fetchOne($sql)) {
			return $result;
		}

		return null;
    }

}
