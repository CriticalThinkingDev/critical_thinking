<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Download Detail model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.6
 */

class Pisc_Downloadplus_Model_Download_Detail extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_download_detail';

	const TYPE_DOWNLOADABLE_LINK = 'link:';
	const TYPE_DOWNLOADABLE_LINK_SAMPLE = 'linkSample:';
	const TYPE_DOWNLOADABLE_SAMPLE = 'sample:';
	const TYPE_DOWNLOADABLE_CUSTOMER = 'customer:';
	const TYPE_DOWNLOADABLE_PRODUCT = 'product:';

	const TYPE_SOURCE_PURCHASED = 'purchased';
	const TYPE_SOURCE_CUSTOMER = 'customer';

	protected $_product = null;
	protected $_currentFiles = null;

    /**
     * Constructor
     *
     */
    public function _construct()
    {
        $this->_init('downloadplus/download_detail');
        $this->initialize();

        parent::_construct();
    }

    public function setProduct($product)
    {
    	$this->_product = $product;
    	return $this;
    }

    public function load($id, $field=null)
    {
    	if ($id) {
    		parent::load($id, $field);
    	} else {
    		return $this->create($file);
    	}

    	$this->_updateFileData();
    	return $this;
    }

    public function initialize()
    {
    	$this->setData('detail_id', null);
    	$this->setData('store_id', null);
    	$this->setData('file', null);
    	$this->setData('base_path', null);
    	$this->setData('product_id', null);
       	$this->setData('link_id', null);
       	$this->setData('link_sample_id', null);
       	$this->setData('sample_id', null);
       	$this->setData('link_purchased_item_id', null);
       	$this->setData('active', 0);
       	$this->setData('version', null);
       	$this->setData('detail', null);

    	$this->setData('filename', null);
    	$this->setData('timestamp', null);
    	$this->setData('size', null);
    	$this->setData('size_formatted', null);
       	$this->setData('type', null);

    	return $this;
    }

    public function create($file, $base_path=null)
    {
    	$this->setData('base_path', $base_path);
    	$this->setData('product_id', null);
    	$this->setData('link_id', null);
    	$this->setData('link_sample_id', null);
    	$this->setData('sample_id', null);
    	$this->setData('link_purchased_item_id', null);
    	$this->setData('link_product_item_id', null);

    	if (empty($file)) {
    		return $this;
    	}

    	if (!is_null($base_path)) {
    		$file = str_replace('/', DS, $base_path.$file);
    	} else {
    		$file = $this->convertTypeToResource($file);
    	}

    	// Determine base path if part of file path
    	$helper = Mage::app()->getHelper('downloadplus/files');
    	// If file is LINK
    	if ($helper->isInPath($file, Mage_Downloadable_Model_Link::getBasePath())) {
    		$this->setData('base_path', Mage_Downloadable_Model_Link::getBasePath());
    		$file = $helper->removePath($file, $this->getBasePath());
    		$model = Mage::getModel('downloadable/link')->load($this->getResource()->clearFilter()->getLinkIdByFile($file));

    		$this->setData('link_id', $model->getId());
    		if (is_null($this->getLinkId())) {
    			// Mark this entry as  Link related
    			$this->setData('link_id', 0);
    		} else {
    			$this->setData('product_id', $model->getProductId());
    		}
    	}
    	// If file is LINK-SAMPLE
    	if ($helper->isInPath($file, Mage_Downloadable_Model_Link::getBaseSamplePath())) {
    		$this->setData('base_path', Mage_Downloadable_Model_Link::getBaseSamplePath());
    		$file = $helper->removePath($file, $this->getBasePath());
    		$model = Mage::getModel('downloadable/link')->load($this->getResource()->clearFilter()->getLinkIdBySampleFile($file));

    		$this->setData('link_sample_id', $model->getId());
    		if (is_null($this->getLinkSampleId())) {
    			// Mark this entry as  Link Sample related
    			$this->setData('link_sample_id', 0);
    		} else {
    			$this->setData('product_id', $model->getProductId());
    		}
       	}
       	// If file is SAMPLE
    	if ($helper->isInPath($file, Mage_Downloadable_Model_Sample::getBasePath())) {
    		$this->setData('base_path', Mage_Downloadable_Model_Sample::getBasePath());
    		$file = $helper->removePath($file, $this->getBasePath());
    		$model = Mage::getModel('downloadable/sample')->load($this->getResource()->clearFilter()->getSampleIdByFile($file));

    		$this->setData('sample_id', $model->getId());
    		if (is_null($this->getSampleId())) {
    			// Mark this entry as  Link Sample related
    			$this->setData('sample_id', 0);
    		} else {
    			$this->setData('product_id', $model->getProductId());
    		}
    	}

    	// If file is a Customer Purchased Link related File
    	if ($helper->isInPath($file, Pisc_Downloadplus_Model_Customer_Download::getBasePath())) {
    		$this->setData('base_path', Pisc_Downloadplus_Model_Customer_Download::getBasePath());
    		$file = $helper->removePath($file, $this->getBasePath());
    		$model = Mage::getModel('downloadplus/link_customer_item')->load($this->getResource()->clearFilter()->getLinkCustomerItemIdByFile($file));

    		$this->setData('link_customer_item_id', $model->getId());
    		if (is_null($this->getLinkCustomerItemId())) {
    			// Mark this entry as  Purchased Item related
    			$this->setData('link_customer_item_id', 0);
    		} else {
    			$this->setData('product_id', $model->getProductId());
    		}
       	}

    	// If file is a Product related File
    	if ($helper->isInPath($file, Pisc_Downloadplus_Model_Product_Download::getBasePath())) {
    		$this->setData('base_path', Pisc_Downloadplus_Model_Product_Download::getBasePath());
    		$file = $helper->removePath($file, $this->getBasePath());
    		$model = Mage::getModel('downloadplus/link_product_item')->load($this->getResource()->clearFilter()->getLinkProductItemIdByFile($file));

    		$this->setData('link_product_item_id', $model->getId());
    		if (is_null($this->getLinkProductItemId())) {
    			// Mark this entry as  Purchased Item related
    			$this->setData('link_product_item_id', 0);
    		} else {
    			$this->setData('product_id', $model->getProductId());
    		}
    	}

    	$this->setData('file', str_replace(DS, '/', $file));
    	$this->setData('active', 0);

    	$this->_updateFileData();
    	return $this;
    }

    public function save()
    {
    	/*
    	 * When resetting StoreID to NULL, remove store specific entry
    	 */
    	if (!is_null($this->getOrigData('store_id')) && is_null($this->getData('store_id'))) {
    		$this->delete();
    		return false;
    	}
    	/*
    	 *  When saving the model and StoreID is set,
    	 *  we then need to keep the default model which has StoreID NULL in the DB
    	 */
    	if (is_null($this->getOrigData('store_id')) && !is_null($this->getData('store_id')) && !is_null($this->getId())) {
    		$this->setId(null);
    	}
    	return parent::save();
    }

    /*
     * Returns boolean if file is assigned
     */
    public function isAssigned()
    {
    	$result = (!is_null($this->getLinkId()) && $this->getLinkId()>0);
    	$result = $result || (!is_null($this->getLinkSampleId()) && $this->getLinkSampleId()>0);
    	$result = $result || (!is_null($this->getSampleId()) && $this->getSampleId()>0);
    	return $result;
    }

    /*
     * Returns boolean if file is active Link/Sample
     */
    public function isActive()
    {
    	return ($this->getActive()==1);
    }

    /*
     * Tags a file as Active
     */
    public function makeActive()
    {
    	$this->setData('active', 1);

    	return $this;
    }

    /*
     * Tags a file as Historical
     */
    public function makeHistorical()
    {
    	$this->setData('active', 0);

    	return $this;
    }

    /*
     * Removes association of the Detail to any Link/Sample
     */
    public function removeAssociation()
    {
    	$this->setData('product_id', null);
    	$this->setData('link_id', null);
    	$this->setData('link_sample_id', null);
    	$this->setData('sample_id', null);
    	$this->setData('link_purchased_item_id', null);
    	$this->setData('link_product_item_id', null);
    	$this->setData('active', 0);

    	return $this;
    }

    /*
     * Loads the object by filename
     */
    public function loadByFile($file, $store=null)
    {
    	if ($file) {
    		$resource = $this->convertTypeToResource($file);
	    	if ($result = $this->getResource()->clearFilter()->addStoreToFilter($store)->getIdByFile($resource)) {
	    		return $this->load($result);
	    	} else {
	    		if (!is_null($store)) {
	    			if ($result = $this->getResource()->clearFilter()->addStoreToFilter(null)->getIdByFile($resource)) {
	    				return $this->load($result);
	    			}
	    		}
	    		return $this->create($file);
	    	}
    	}

    	return $this->initialize();
    }

    /*
     * Loads the object by the LinkId
     */
    public function loadByLinkId($id, $store=null)
    {
    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter($store)->getIdByLinkId($id)) {
    		return $this->load($result);
    	}
    	// Load default if necessary
    	if (!is_null($store)) {
	    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter(null)->getIdByLinkId($id)) {
	    		return $this->load($result);
	    	}
    	}

    	return $this->initialize();
    }

    /*
     * Loads the object by the LinkSampleId
     */
    public function loadByLinkSampleId($id, $store=null)
    {
    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter($store)->getIdByLinkSampleId($id)) {
    		return $this->load($result);
    	}
    	// Load default if necessary
    	if (!is_null($store)) {
	    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter(null)->getIdByLinkSampleId($id)) {
	    		return $this->load($result);
	    	}
    	}

    	return $this->initialize();
    }

    /*
     * Loads the object by the SampleId
     */
    public function loadBySampleId($id, $store=null)
    {
    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter($store)->getIdBySampleId($id)) {
    		return $this->load($result);
    	}
    	// Load default if necessary
    	if (!is_null($store)) {
	    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter(null)->getIdBySampleId($id)) {
	    		return $this->load($result);
	    	}
    	}

    	return $this->initialize();
    }

    /*
     * Loads the object by the LinkPurchasedItemId
     */
    public function loadByLinkCustomerItemId($id, $store=null)
    {
    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter($store)->getIdByLinkCustomerItemId($id)) {
    		return $this->load($result);
    	}
    	// Load default if necessary
    	if (!is_null($store)) {
	    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter(null)->getIdByLinkCustomerItemId($id)) {
	    		return $this->load($result);
	    	}
    	}

    	return $this->initialize();
    }

    /*
     * Loads the object by the LinkPurchasedItemId
     */
    public function loadByLinkProductItemId($id, $store=null)
    {
    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter($store)->getIdByLinkProductItemId($id)) {
    		return $this->load($result);
    	}
    	// Load default if necessary
    	if (!is_null($store)) {
	    	if ($result = $this->getResource()->clearFilter()->addActiveToFilter()->addStoreToFilter(null)->getIdByLinkProductItemId($id)) {
	    		return $this->load($result);
	    	}
    	}

    	return $this->initialize();
    }

    /*
     * Returns boolean if this File as related archived files
     */
    public function hasArchive()
    {
    	$result = false;
    	$files = null;

    	if ($this->getLinkId()) {
	    	$files = $this->getCollection()
	    				->clearFilter()
	    				->addLinkToFilter($this->getLinkId())
	    				->getHistoricalFiles();
	    	$result = !empty($files);
    	}
    	if ($this->getLinkSampleId()) {
	    	$files = $this->getCollection()
	    				->clearFilter()
	    				->addLinkSampleToFilter($this->getLinkId())
	    				->getHistoricalFiles();
	    	$result = !empty($files);
    	}
    	if ($this->getSampleId()) {
	    	$files = $this->getCollection()
	    				->clearFilter()
	    				->addSampleToFilter($this->getSampleId())
	    				->getHistoricalFiles();
	    	$result = !empty($files);
    	}

    	return $result;
    }

    private function _updateFileData()
    {
    	// Update data from physical file
    	if (!is_null($this->getLinkId()) && !$this->getBasePath()) {
    		$this->setData('base_path', Mage_Downloadable_Model_Link::getBasePath());
    	}
    	if (!is_null($this->getLinkSampleId()) && !$this->getBasePath()) {
    		$this->setData('base_path', Mage_Downloadable_Model_Link::getBaseSamplePath());
    	}
    	if (!is_null($this->getSampleId()) && !$this->getBasePath()) {
    		$this->setData('base_path', Mage_Downloadable_Model_Sample::getBasePath());
    	}
    	if (!is_null($this->getLinkPurchasedItemId()) && !$this->getBasePath()) {
    		$this->setData('base_path', Pisc_Downloadplus_Model_Customer_Download::getBasePath());
    	}
    	if (!is_null($this->getLinkProductItemId()) && !$this->getBasePath()) {
    		$this->setData('base_path', Pisc_Downloadplus_Model_Product_Download::getBasePath());
    	}

    	// Update relation to Links/Samples
    	if ($this->getLinkId()==0) {
    		$id = Mage::getModel('downloadplus/link')->getIdByFile($this->getFile());
    		if (!is_null($id)) {
    			$this->setData('link_id', $id);
    		}
    	}
    	if ($this->getLinkSampleId()==0) {
    		$id = Mage::getModel('downloadplus/link')->getIdBySampleFile($this->getFile());
    		if (!is_null($id)) {
    			$this->setData('link_sample_id', $id);
    		}
    	}
    	if ($this->getSampleId()==0) {
    		$id = Mage::getModel('downloadplus/sample')->getIdByFile($this->getFile());
    		if (!is_null($id)) {
    			$this->setData('sample_id', $id);
    		}
    	}
    	if ($this->getLinkCustomerItemId()==0) {
    		$id = Mage::getModel('downloadplus/link_customer_item')->getIdByFile($this->getFile());
    		if (!is_null($id)) {
    			$this->setData('link_customer_item_id', $id);
    		}
    	}
    	if ($this->getLinkProductItemId()==0) {
    		$id = Mage::getModel('downloadplus/link_product_item')->getIdByFile($this->getFile());
    		if (!is_null($id)) {
    			$this->setData('link_product_item_id', $id);
    		}
    	}

    	$this->setData('filename', null);
    	$this->setData('timestamp', null);
    	$this->setData('size', null);
    	$this->setData('size_formatted', null);
    	$this->setData('type', null);

    	if ($this->getFile()) {
	    	$file = Mage::getModel('downloadplus/type_file')->loadResource($this->getBasePath(), $this->getFile());
	    	$helper = Mage::app()->getHelper('downloadplus/data');
	    	if ($file->exists()) {
		    	$this->setData('filename', basename($this->getData('file')));
		    	$this->setData('timestamp', Mage::getModel('core/date')->timestamp($file->getTimeStamp()));
		    	$this->setData('size', $file->getSize());
		    	$this->setData('size_formatted', $helper->getBytesFormatted($file->getSize()));
		    	$this->setData('type', $this->convertFileToType());
	    	}
    	}

    }


   /*
    * Converts a type:file notation to a full path/file resource
    */
   public function convertTypeToResource($type)
   {
   		$result = $type;
   		$helper = Mage::app()->getHelper('downloadplus/files');

   		if ($helper->isInPath($type, self::TYPE_DOWNLOADABLE_LINK)) {
   			$result = Mage::getModel('downloadable/link')->getBasePath().$helper->removePath($type, self::TYPE_DOWNLOADABLE_LINK);
   		}
   		if ($helper->isInPath($type, self::TYPE_DOWNLOADABLE_LINK_SAMPLE)) {
   			$result = Mage::getModel('downloadable/link')->getBaseSamplePath().$helper->removePath($type, self::TYPE_DOWNLOADABLE_LINK_SAMPLE);
   		}
   		if ($helper->isInPath($type, self::TYPE_DOWNLOADABLE_SAMPLE)) {
   			$result = Mage::getModel('downloadable/sample')->getBasePath().$helper->removePath($type, self::TYPE_DOWNLOADABLE_SAMPLE);
   		}
   		if ($helper->isInPath($type, self::TYPE_DOWNLOADABLE_CUSTOMER)) {
   			$result = Mage::getModel('downloadplus/customer_download')->getBasePath().$helper->removePath($type, self::TYPE_DOWNLOADABLE_CUSTOMER);
   		}
   		if ($helper->isInPath($type, self::TYPE_DOWNLOADABLE_PRODUCT)) {
   			$result = Mage::getModel('downloadplus/product_download')->getBasePath().$helper->removePath($type, self::TYPE_DOWNLOADABLE_PRODUCT);
   		}
   		$result = str_replace("/", DS, $result);

   		return $result;
   }

   public function convertFileToType($file=null)
   {
   		if (is_null($file)) {
   			$file = $this->getFile();
   		}
   		$result = str_replace(DS, "/", $file);
   		if (!is_null($this->getLinkId())) {
   			$result = self::TYPE_DOWNLOADABLE_LINK.$file;
   		}
   		if (!is_null($this->getLinkSampleId())) {
   			$result = self::TYPE_DOWNLOADABLE_LINK_SAMPLE.$file;
   		}
   		if (!is_null($this->getSampleId())) {
   			$result = self::TYPE_DOWNLOADABLE_SAMPLE.$file;
   		}
   		if (!is_null($this->getLinkPurchasedItemId())) {
   			$result = self::TYPE_DOWNLOADABLE_CUSTOMER.$file;
   		}
   		if (!is_null($this->getLinkProductItemId())) {
   			$result = self::TYPE_DOWNLOADABLE_PRODUCT.$file;
   		}

   		return $result;
   }

   /*
    * Returns true if data is default, false when store specific
    */
   public function getUsedDefault()
   {
   		return is_null($this->getData('store_id'));
   }

}