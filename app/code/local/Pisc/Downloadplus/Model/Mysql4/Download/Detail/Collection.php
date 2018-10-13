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
 * @version		0.1.7
 */
class Pisc_Downloadplus_Model_Mysql4_Download_Detail_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

	protected $_product = null;
	protected $_store_id = null;
	protected $_link_id = null;
	protected $_link_sample_id = null;
	protected $_sample_id = null;
	protected $_sort = null;
	protected $_hidden = true;
	protected $_filter_files = false;

	protected $_activeFiles = null;
	protected $_historicalFiles = null;

	/**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
    	$this->_init('downloadplus/download_detail', 'detail_id');
    }

    public function clearFilter()
    {
        $this->_activeFiles = null;
        $this->_historicalFiles = null;

        $this->_product = null;
        $this->_store_id = null;
        $this->_link_id = null;
        $this->_link_sample_id = null;
        $this->_sample_id = null;
        $this->_filter_files = false;

    	return $this;
    }

    public function addProductToFilter($product)
    {
    	$this->_activeFiles = null;
    	$this->_historicalFiles = null;

    	if (empty($product)) {
    		$this->_product = null;
    	} else {
    		$this->_product = $product;
    	}

        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif (is_array($product)) {
            $this->addFieldToFilter('product_id', Array('in' => $product));
        } elseif ($product instanceof Mage_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', Array('eq' => $product->getId()));
        } else {
            $this->addFieldToFilter('product_id', Array('eq' => $product));
        }

        return $this;
    }

    public function addStoreToFilter($store)
    {
    	if (empty($store)) {
    		$this->_store_id = null;
    		return $this;
    	} elseif ($store instanceof Mage_Core_Model_Store) {
    		$this->_store_id = $store->getId();
    	} else {
    		$this->_store_id = $store;
    	}

    	$this->addFieldToFilter('store_id', Array('eq' => $this->_store_id));
    	return $this;
    }

    public function addLinkToFilter($link)
    {
        if (empty($link)) {
            $this->_link_id = null;
        } elseif ($link instanceof Mage_Downloadable_Model_Link || $link instanceof Pisc_Downloadplus_Model_Link) {
            $this->_link_id = $link->getId();
        } else {
            $this->_link_id = $link;
        }

        $this->_sample_id = null;
        return $this;
    }

    public function addLinkSampleToFilter($link)
    {
        if (empty($link)) {
            $this->_link_sample_id = null;
        } elseif ($link instanceof Mage_Downloadable_Model_Link || $link instanceof Pisc_Downloadplus_Model_Link) {
            $this->_link_sample_id = $link->getId();
        } else {
            $this->_link_sample_id = $link;
        }

        $this->_sample_id = null;
        return $this;
    }

    public function addSampleToFilter($sample)
    {
        if (empty($sample)) {
            $this->_sample_id = null;
        } elseif ($sample instanceof Mage_Downloadable_Model_Sample || $sample instanceof Pisc_Downloadplus_Model_Sample) {
            $this->_sample_id = $sample->getId();
        } else {
            $this->_sample_id = $sample;
        }

        $this->_link_id = null;
        return $this;
    }

    public function addFilesToFilter($files)
    {
    	$this->_filter_files = false;

    	if (is_array($files)) {
    		$result = Array();
    		foreach ($files as $file) {
    			if (is_object($file)) {
    				$result[] = $file->getFile();
    			}
    			if (is_string($file)) {
    				$result[] = $file;
    			}
    		}
    		if (count($result)>0) {
    			$this->_filter_files = $result;
    		}
    	}

    	return $this;
    }

    /*
     * Adds filter to include/exclude hidden files
     */
    public function addVisibleToFilter($visible)
    {
    	$this->_hidden = !$visible;
    	return $this;
    }

    public function addSort($sort)
    {
    	$this->_sort = $sort;
    	return $this;
    }

    /**
     * Returns a collection all current files unrelated to Links, Samples or set as Historical File
     *
     * @return Collection of Downloadplus/Details
     */
    public function getRelatedFiles()
    {
    	$this->addSort('file');
    	return array_merge($this->getActiveFiles(), $this->getHistoricalFiles());
    }

    /**
     * Returns a collection of all current active files with details
     *
     * @return Collection of Downloadplus/Details
     */
    public function getActiveFiles()
    {
    	if (!is_null($this->_activeFiles)){
    		return $this->_activeFiles;
    	}

		$collection = Array();

		if (!is_null($this->_link_id)) { $where[] = "link_id=".$this->_link_id; }
		if (!is_null($this->_link_sample_id)) { $where[] = "link_sample_id=".$this->_link_sample_id; }
		if (!is_null($this->_sample_id)) { $where[] = "sample_id=".$this->_sample_id; }
		if (is_null($this->_store_id)) { $where[] = "store_id='0'"; } else { $where[]="store_id='".$this->_store_id."'"; }

		// Also include hidden files?
		if ($this->_hidden===false) { $where[] = "hidden='0'"; }

		$where[] = "link_customer_item_id IS NULL";
		$where[] = "link_product_item_id IS NULL";
		$where[] = "active='1'";

		$sql = clone $this->getSelect();
		if (!empty($where)) {
		    $sql = $sql->where(implode(' AND ', $where));
		}
		if (!empty($this->_sort)) {
		    $sql = $sql->order($this->_sort);
		}

		if ($files = $this->getConnection()->fetchAll($sql)) {
		    foreach ($files as $file) {
		        $item = Mage::getModel('downloadplus/download_detail')->load($file['detail_id']);
		        $collection[] = $item;
		    }
		}

		$this->_activeFiles = $collection;

		return $collection;
    }

    /**
     * Returns a collection of historial files listed in Downloadplus/Download_Details
     */
    public function getHistoricalFiles()
    {
    	if (!is_null($this->_historicalFiles)) {
    		return $this->_historicalFiles;
    	}

    	$collection = Array();

    	// Create filter for all active files
    	$activeFiles = $this->getActiveFiles();
    	$filter = Array();
    	foreach ($activeFiles as $file) {
    		if (!is_null($file->getId())) {
    			$filter[] = $file->getId();
    		}
    	}

    	// Get all files registered in Downloadplus/Download_Detail with filter
    	$where = Array();
    	if (!empty($filter)) { $where[] = "detail_id NOT IN (".implode(',', $filter).")"; }

    	if (!is_null($this->_link_id)) { $where[] = "link_id=".$this->_link_id; }
    	if (!is_null($this->_link_sample_id)) { $where[] = "link_sample_id=".$this->_link_sample_id; }
    	if (!is_null($this->_sample_id)) { $where[] = "sample_id=".$this->_sample_id; }
    	if (is_null($this->_store_id)) { $where[] = "store_id='0'"; } else { $where[]="store_id='".$this->_store_id."'"; }

    	// Also include hidden files?
    	if ($this->_hidden===false) { $where[] = "hidden='0'"; }

    	$where[] = "link_customer_item_id IS NULL";
    	$where[] = "link_product_item_id IS NULL";
    	$where[] = "active='0'";

    	$sql = clone $this->getSelect();
	    if (!empty($where)) {
	    	$sql = $sql->where(implode(' AND ', $where));
	    }
	    if (!empty($this->_sort)) {
	    	$sql = $sql->order($this->_sort);
	    }

		if ($files = $this->getConnection()->fetchAll($sql)) {
			foreach ($files as $file) {
				$item = Mage::getModel('downloadplus/download_detail')->load($file['detail_id']);
				$collection[] = $item;
			}
		}

		return $collection;
    }

    /**
     * Returns a collection of all physical available files associated to Downloadable Links
     */
    public function getAvailablePhysicalLinkFiles()
    {
    	$collection = Array();
    	$helper = Mage::app()->getHelper('downloadplus/files');

    	// Downloadable Links
    	$files = $helper->getDirRecursive(Mage_Downloadable_Model_Link::getBasePath());
    	if (is_array($files)) {
	    	sort($files);
	    	foreach ($files as $file) {
	    		// make relative Path
	    		$file = str_replace(Mage_Downloadable_Model_Link::getBasePath(), '', $file);
	    		// unify directory slash
	    		$file = str_replace(DS, '/', $file);
	    		if (!$this->_filter_files || ($this->_filter_files && !in_array($file, $this->_filter_files))) {
	    			$collection[] = Mage::getModel('downloadplus/download_detail')->create($file, Mage_Downloadable_Model_Link::getBasePath());
	    		}
	    	}
    	}

    	return $collection;
    }

    /**
     * Returns a collection of all physical available files associated to Downloadable Link Samples
     */
    public function getAvailablePhysicalLinkSampleFiles()
    {
    	$collection = Array();
    	$helper = Mage::app()->getHelper('downloadplus/files');

    	// Downloadable Links
    	$files = $helper->getDirRecursive(Mage_Downloadable_Model_Link::getBaseSamplePath());
    	if (is_array($files)) {
	    	sort($files);
	    	foreach ($files as $file) {
	    		// make relative Path
	    		$file = str_replace(Mage_Downloadable_Model_Link::getBaseSamplePath(), '', $file);
	    		// unify directory slash
	    		$file = str_replace(DS, '/', $file);
	    		if (!$this->_filter_files || ($this->_filter_files && !in_array($file, $this->_filter_files))) {
	    			$collection[] = Mage::getModel('downloadplus/download_detail')->create($file, Mage_Downloadable_Model_Link::getBaseSamplePath());
	    		}
	    	}
    	}

    	return $collection;
    }

    /**
     * Returns a collection of all physical available files associated to Downloadable Samples
     */
    public function getAvailablePhysicalSampleFiles()
    {
    	$collection = Array();
    	$helper = Mage::app()->getHelper('downloadplus/files');

    	// Downloadable Links
    	$files = $helper->getDirRecursive(Mage_Downloadable_Model_Sample::getBasePath());
    	if (is_array($files)) {
	    	sort($files);
	    	foreach ($files as $file) {
	    		// make relative Path
	    		$file = str_replace(Mage_Downloadable_Model_Sample::getBasePath(), '', $file);
	    		// unify directory slash
	    		$file = str_replace(DS, '/', $file);
	    		if (!$this->_filter_files || ($this->_filter_files && !in_array($file, $this->_filter_files))) {
	    			$collection[] = Mage::getModel('downloadplus/download_detail')->create($file, Mage_Downloadable_Model_Sample::getBasePath());
	    		}
	    	}
    	}

    	return $collection;
    }

}
