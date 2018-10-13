<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license     Commercial Unlimited License
 * @version		0.1.5
 */

/**
 * Downloadplus Product Serialnumber model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 */

class Pisc_Downloadplus_Model_Product_Serialnumber extends Mage_Core_Model_Abstract
{

	protected $_eventPrefix = 'downloadplus_product_serialnumber';
	
	/**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('downloadplus/product_serialnumber');
        parent::_construct();
    }

    /*
    * Set serialnumber pool
    */
    public function setSerialNumberPool($data)
    {
    	Mage::getModel('downloadplus/config');
    	
    	if (empty($data)) {
    		$this->setData('serial_number_pool', Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE);
    	} else {
	    	if ($data==Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE || $data==Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT) {
	    		$this->setData('serial_number_pool', $data);
	    	} else {
	    		$this->setData('serial_number_pool', strtoupper($data));
	    	}
    	}
    	return $this;
    }

    public function getSerialNumberPool()
    {
    	Mage::getModel('downloadplus/config');
    	 
    	$data = $this->getData('serial_number_pool');
    	if (empty($data) || $data==Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE) {
    		$data = false;
    	}
    	return $data;
    }

    /*
     * Returns bool if serialnumbers are used
     */
    public function hasSerialnumbers()
    {
    	$result = ($this->getSerialNumberPool()!=false);
    	return $result;
    }
    
    public function isGlobal($value)
    {
    	return (is_null($this->getProductId()));
    }
    
    /*
     * Returns a hash for this serial number as id
     */
    public function getSerialHash()
    {
    	$result = $this->getData('serial_hash');
    	if (empty($result)) {
    		$result = $this->createSerialHash($this->getProductId(), $this->getSerialNumber());
    		$this->setData('serial_hash', $result);
    	}
    	return $result;
    }

    /*
	 * Creates a serial hash
	 */
    public function createSerialHash($productId, $serial_number)
    {
    	$result = md5($productId.$serial_number);
    	return $result;
    }

    /*
     * Loads the next available Serialnumber for Link
     */
    public function loadNextByLink($link)
    {
    	$this->initialize();
    	
    	if (is_numeric($link)) {
    		$link = Mage::getModel('downloadable/link')->load($link);
    	}
    	
    	$extension = Mage::getModel('downloadplus/link_extension')->loadByLink($link);
    	if ($extension->hasSerialnumbers()) {
    		$pool = $extension->getSerialNumberPool();
	    	if ($pool!=Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT) {
	    		$sql = $this->_getResource()->getReadConnection()
	    					->select()
							->from($this->_getResource()->getMainTable())
	    					->where("product_id=?", $link->getProductId())
	    					->where("serial_number_pool=?", $pool)
	    					->order("created_at ASC");
	    		
	    		if ($item = $this->_getResource()->getReadConnection()->fetchOne($sql)) {
	    			$this->load($item);
	    		}
	    	} else {
	    		$this->loadNextByProduct($link->getProductId());
	    	}
    	}
    	
    	return $this;
    }
    
    /*
     * Loads the next available Serialnumber for Product
     */
    public function loadNextByProduct($product)
    {
	    $this->initialize();
	    
	    Mage::getModel('downloadplus/config');

    	$id = null;
    	if ($product instanceof Mage_Catalog_Model_Product) {
    		$id = $product->getId();
    	} else {
    		$id = $product;
    	}

    	if ($id) {
    		$sql = $this->_getResource()->getReadConnection()
    				->select()
					->from($this->_getResource()->getMainTable())
    				->where("product_id=?", $id)
    				->where("serial_number_pool IS NULL OR serial_number_pool=?", Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT)
					->order("created_at ASC");

			if ($item = $this->_getResource()->getReadConnection()->fetchOne($sql)) {
				$this->load($item);
			}
    	}

		return $this;
    }

    /*
     * Returns a count of available serialnumbers for a product
     */
    public function getCountByProduct($product)
    {
    	Mage::getModel('downloadplus/config');
    	
    	$result = 0;
    	$id = null;
    	if ($product instanceof Mage_Catalog_Model_Product) {
    		$id = $product->getId();
    	} else {
    		$id = $product;
    	}

    	if ($id) {
    		$sql = $this->_getResource()->getReadConnection()
    				->select()
					->from($this->_getResource()->getMainTable())
    				->where("product_id=?", $id)
    				->where("serial_number_pool IS NULL OR serial_number_pool=?", Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT);
    		
			if ($items = $this->_getResource()->getReadConnection()->fetchAll($sql)) {
		        $result = count($items);
			}
    	}

		return $result;
    }

    /*
     * Returns a count of available serialnumbers for a link
     */
    public function getCountByLink($link)
    {
    	Mage::getModel('downloadplus/config');
    	
    	$result = 0;
    	if (is_numeric($link)) {
    		$link = Mage::getModel('downloadable/link')->load($link);
    	}
    	
    	$extension = Mage::getModel('downloadplus/link_extension')->loadByLink($link);
    	if ($extension->hasSerialNumberPool()) {
    		$pool = $extension->getSerialNumberPool(); 
	    	if ($pool!=Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT) {
    			$sql = $this->_getResource()->getReadConnection()
    					->select()
						->from($this->_getResource()->getMainTable())
    					->where("product_id=?", $link->getProductId())
    					->where("serial_number_pool=?", $extension->getSerialNumberPool());
    		} else {
    			$sql = $this->_getResource()->getReadConnection()
    					->select()
						->from($this->_getResource()->getMainTable())
    					->where("product_id=?", $link->getProductId())
    					->where("serial_number_pool IS NULL OR serial_number_pool=?", Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT);
    		}
    		if ($items = $this->_getResource()->getReadConnection()->fetchAll($sql)) {
    			$result = count($items);
    		}
    	}
    	
    	return $result;
    }
    
    /*
     * Clears current data
     */
    public function initialize()
    {
    	$this->set('serial_hash', null);
    	$this->set('product_id', null);
    	$this->set('serial_number_pool', null);
    	$this->set('serial_number', null);
    	$this->set('created_at', null);

    	return $this;
    }

    /*
     * Save the serialnumber
     */
    public function save()
    {
    	$this->getSerialHash();
    	if (!$this->getCreatedAt()) {
    		$this->setCreatedAt(now());
    	}
    	parent::save();
    }

    /*
     * Returns if a serialnumber with current data already exists
     */
    public function exists()
    {	
    	$result = false;

    	$sql = $this->_getResource()->getReadConnection()
	    	->select()
	    	->from($this->_getResource()->getMainTable())
	    	->where("serial_hash=?", $this->getSerialHash());

    	if ($items = $this->_getResource()->getReadConnection()->fetchAll($sql)) {
    		$result = count($items)>0;
    	}
	    	
    	return $result;
    }
    
    /*
     * Loads the model by a Serialnumber
     */
    public function loadBySerialnumber($serial)
    {
    	$this->initialize();
    	if (empty($serial)) { return $this; }
    	
    	$collection = $this->getCollection();
    	$collection->getSelect()->where('serial_number=?', $serial);
    	if ($collection->getSize()>0) {
    		$item = $collection->getFirstItem();
    		if ($item->getSerialHash()) {
    			$this->load($item->getSerialHash());
    		}
    	}
    	
    	return $this;
    }
    
}