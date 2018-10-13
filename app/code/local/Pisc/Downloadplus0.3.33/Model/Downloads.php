<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloads model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.2.4
 */
class Pisc_Downloadplus_Model_Downloads extends Mage_Core_Model_Abstract
{
	protected $_updatesLinks = null;
	protected $_updatesLinkSamples = null;
	protected $_updatesSamples = null;
	protected $_topLinks = null;
	protected $_topSamples = null;
	protected $_filter_product = null;
	protected $_filter_category = null;
	protected $_store_id = null;

	protected $_eventPrefix = 'downloadplus_downloads';
	
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('downloadable/link');
        parent::_construct();
    }

    public function addProductToFilter($product)
    {
    	$this->_filter_product = $product;
    	return $this;
    }

    public function setProduct($product)
    {
    	$this->addProductToFilter($product);
    	return $this;
    }

    public function addCategoryToFilter($category)
    {
    	$this->_filter_category = $category;
    	return $this;
    }

    public function setStoreId($storeId)
    {
    	$this->_store_id = $storeId;
    	return $this;
    }

    /*
     * Returns a list of files and their timestamp for Product Links
     */
    public function getLinkUpdates()
    {
    	if (!is_null($this->_updatesLinks)) {
    		return $this->_updatesLinks;
    	}

    	$this->_updatesLinks = Array();

    	$links = Mage::getModel('downloadable/link')->getCollection();
    	if ($this->_filter_product) {
    		$links->addProductToFilter($this->_filter_product);
    	}

    	foreach ($links as $link) {
    		if ($link->getLinkFile()) {
    			$load = true;
    			// Applying filters
    			if ($this->_store_id || $this->_filter_category) {
    				$product = Mage::getModel('catalog/product')->load($link->getProductId());
    				if ($this->_store_id) {
    					$load = $load && in_array($this->_store_id, $product->getStoreIds());
    				}
    				if ($this->_filter_category) {
    					$load = $load && in_array($this->_filter_category->getId(), $product->getCategoryIds());
    				}
    			}

    			if ($load) {
	    			$file = Mage::getModel('downloadplus/type_file');
	    			$file->loadByLink($link);
	    			if ($file->exists()) {
		    			$detail = Mage::getModel('downloadplus/download_detail');
		    			$detail->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK.$link->getLinkFile(), $this->_store_id);
			    		$data = Array(
			    			'file' => $file->getFile(),
			    			'path' => $file->getPath(),
			    			'pathfile' => $file->getPathFile(),
			    			'size' => $file->getSize(),
			    			'timestamp' => $file->getTimeStamp(),
			    			'title' => $file->getTitle(),
			    			'product' => $file->getProduct(),
			    		    'detail' => $detail
			    		);
			    		$this->_updatesLinks[] = $data;
	    			}
	    		}
    		}
    	}

    	$this->_updatesLinks = $this->sort($this->_updatesLinks, 'timestamp', false);

    	return $this->_updatesLinks;
    }

    /*
     * Returns a list of files and their timestamp for Product Links
     */
    public function getLinkSampleUpdates()
    {
    	if (!is_null($this->_updatesLinkSamples)) {
    		return $this->_updatesLinkSamples;
    	}

    	$this->_updatesLinkSamples = Array();

    	$links = Mage::getModel('downloadable/link')->getCollection();
    	if ($this->_filter_product) {
    		$links = $links->addProductToFilter($this->_filter_product);
    	}

    	foreach ($links as $link) {
    		if ($link->getSampleFile()) {
    			$load = true;
    			// Applying filters
    			if ($this->_store_id || $this->_filter_category) {
    				$product = Mage::getModel('catalog/product')->load($link->getProductId());
    				if ($this->_store_id) {
    					$load = $load && in_array($this->_store_id, $product->getStoreIds());
    				}
    				if ($this->_filter_category) {
    					$load = $load && in_array($this->_filter_category->getId(), $product->getCategoryIds());
    				}
    			}

    			if ($load) {
	    			$file = Mage::getModel('downloadplus/type_file');
	    			$file->loadByLinkSample($link);
		    		if ($file->exists()) {
		    			$detail = Mage::getModel('downloadplus/download_detail');
		    			$detail->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK_SAMPLE.$link->getSampleFile(), $this->_store_id);
			    		$data = Array(
			    			'file' => $file->getFile(),
			    			'path' => $file->getPath(),
			    			'pathfile' => $file->getPathFile(),
			    			'size' => $file->getSize(),
			    			'timestamp' => $file->getTimeStamp(),
			    			'title' => $file->getTitle(),
			    			'product' => $file->getProduct(),
			    		    'detail' => $detail
			    		);
			    		$this->_updatesLinkSamples[] = $data;
		    		}
    			}
    		}
    	}

    	$this->_updatesLinkSamples = $this->sort($this->_updatesLinkSamples, 'timestamp', false);

    	return $this->_updatesLinkSamples;
    }

    /*
     * Returns a list of files and their timestamp for Samples
     */
    public function getSampleUpdates()
    {
    	if (!is_null($this->_updatesSamples)) {
    		return $this->_updatesSamples;
    	}

    	$this->_updatesSamples = Array();

    	$samples = Mage::getModel('downloadable/sample')->getCollection();
    	if ($this->_filter_product) {
    		$samples = $samples->addProductToFilter($this->_filter_product);
    	}

    	foreach ($samples as $sample) {
    		$file = Mage::getModel('downloadplus/type_file');
    		if ($sample->getSampleFile()) {
    			$load = true;
    			// Applying filters
    			if ($this->_store_id || $this->_filter_category) {
    				$product = Mage::getModel('catalog/product')->load($sample->getProductId());
    				if ($this->_store_id) {
    					$load = $load && in_array($this->_store_id, $product->getStoreIds());
    				}
    				if ($this->_filter_category) {
    					$load = $load && in_array($this->_filter_category->getId(), $product->getCategoryIds());
    				}
    			}

    			if ($load) {
	    			$file = Mage::getModel('downloadplus/type_file');
	    			$file->loadBySample($sample);
		    		if ($file->exists()) {
		    			$detail = Mage::getModel('downloadplus/download_detail');
		    			$detail->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_SAMPLE.$sample->getSampleFile(), $this->_store_id);
		    			$data = Array(
			    			'file' => $file->getFile(),
			    			'path' => $file->getPath(),
			    			'pathfile' => $file->getPathFile(),
			    			'size' => $file->getSize(),
			    			'timestamp' => $file->getTimeStamp(),
			    			'title' => $file->getTitle(),
			    			'product' => $file->getProduct(),
			    		    'detail' => $detail
			    		);
			    		$this->_updatesSamples[] = $data;
		    		}
    			}
    		}
    	}

    	$this->_updatesSamples = $this->sort($this->_updatesSamples, 'timestamp', false);

    	return $this->_updatesSamples;
    }

    /*
     * Returns a list of files and their timestamps for Links and Samples ordered by timestamp
     */
    public function getUpdates()
    {
    	$result = Array();
    	if (is_null($this->_updatesLinks)) {
    		$this->getLinkUpdates();
    	}
    	if (is_null($this->_updatesSamples)) {
    		$this->getSampleUpdates();
    	}
    	if (!is_null($this->_updatesLinks)) {
    		$result = array_merge($result, $this->_updatesLinks);
    	}
    	if (!is_null($this->_updatesUpdates)) {
    		$result = array_merge($result, $this->_updatesSamples);
    	}

    	return $this->sort($result, 'timestamp', false);
    }

    /*
     * Returns a list of Downloadable Products with totals
     */
    public function getTopLinks()
    {
    	if (!is_null($this->_topLinks)) {
    		return $this->_topLinks;
    	}

    	$collection = Mage::getModel('downloadplus/log')->getCollection()
    		->addStoreToFilter($this->_store_id)
	    	->getTopDownloadProducts()
	    	->getItems();

	    $this->_topLinks = Array();

  	  	foreach ($collection as $item) {
  	  		$product = false;
  	  		if ($item->getData('product_id')!='') {
  	  			$product = Mage::getModel('catalog/product');
  	  			$product->load($item->getData('product_id'));
	  	  	} else {
		  	  	if ($item->getData('product_sku')!='') {
		  	  		$product = Mage::getModel('catalog/product');
		  	  		$product->load($product->getIdBySku($item->getData('product_sku')));
		  	  	}
	  	  	}
    		// Applying Filters
	  	  	if ($product && $this->_store_id) {
	  	  		if (!in_array($this->_store_id, $product->getStoreIds())) {
	  	  			$product = false;
	  	  		}
	  	  	}
	  	  	if ($product && $this->_filter_category) {
	  	  		if (!in_array($this->_filter_category->getId(), $product->getCategoryIds())) {
	  	  			$product = false;
	  	  		}
	  	  	}
	  	  	if ($product) {
	  	  		$data = Array(
	  	  				'title' => 	$item->getData('title'),
	  	  				'total'	=>	$item->getData('total'),
	  	  				'product' => $product
	  	  			);
	  	  		$this->_topLinks[] = $data;
	  	  	}
  	  	}
  	  	$this->_topLinks = $this->sort($this->_topLinks, 'total', false);

  	  	return $this->_topLinks;
    }

    /*
     * Returns a list of Downloadable Samples with totals
     */
    public function getTopSamples()
    {
    	if (!is_null($this->_topSamples)) {
    		return $this->_topSamples;
    	}

    	$collection = Mage::getModel('downloadplus/log')->getCollection()
    		->addStoreToFilter($this->_store)
    		->getTopDownloadSamples()
	      	->getItems();

	    $this->_topSamples = Array();

	    foreach ($collection as $item) {
  	  		$product = false;
  	  		if ($item->getData('product_id')!='') {
  	  			$product = Mage::getModel('catalog/product');
  	  			$product->load($item->getData('product_id'));
	  	  	} else {
		  	  	if ($item->getData('product_sku')!='') {
		  	  		$product = Mage::getModel('catalog/product');
		  	  		$product->load($product->getIdBySku($item->getData('product_sku')));
		  	  	}
	  	  	}
    		// Applying Filters
	  	  	if ($product && $this->_store_id) {
	  	  		if (!in_array($this->_store_id, $product->getStoreIds())) {
	  	  			$product = false;
	  	  		}
	  	  	}
	  	  	if ($product && $this->_filter_category) {
	  	  		if (!in_array($this->_filter_category->getId(), $product->getCategoryIds())) {
	  	  			$product = false;
	  	  		}
	  	  	}

	  	  	if ($product) {
	  	  		$data = Array(
	  	  				'title' => 	$item->getData('title'),
	  	  				'total'	=>	$item->getData('total'),
	  	  				'product' => $product
	  	  			);
	  	  		$this->_topSamples[] = $data;
	  	  	}
	    }

	  	$this->_topSamples = $this->sort($this->_topSamples, 'total', false);

	  	return $this->_topSamples;
    }

    /*
     * Returns a list of Downloads with totals
     */
    public function getTopDownloads()
    {
    	$result = Array();
    	if (is_null($this->_topLinks)) {
    		$this->getTopLinks();
    	}
    	if (is_null($this->_topSamples)) {
    		$this->getTopSamples();
    	}
    	if (!is_null($this->_topLinks)) {
    		$result = array_merge($result, $this->_topLinks);
    	}
    	if (!is_null($this->_topSamples)) {
    		$result = array_merge($result, $this->_topSamples);
    	}

    	return $this->sort($result, 'total', false);
    }

    /*
     * Utility function: Sort Array by Key
     */
    public function sort($array, $subkey, $ascending=true) {
    	// Return Array if empty
    	if (is_null($array) || count($array)==0) {
    		return $array;
    	}

    	$b = Array();
    	$c = Array();

    	foreach($array as $k=>$v) {
			$b[$k] = strtolower($v[$subkey]);
		}
		if ($ascending) {
			asort($b);
		} else {
			arsort($b);
		}
		foreach($b as $key=>$val) {
			$c[] = $array[$key];
		}
		return $c;
    }

}