<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloadable Link Extension model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.7
 */

class Pisc_Downloadplus_Model_Link_Extension extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_link_extension';
	
	const EXPIRE_ON_NEVER = 'never';
	const EXPIRE_ON_ORDER = 'onorder';
	const EXPIRE_ON_DOWNLOAD = 'ondownload';
	
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        //parent::_construct();
        $this->_init('downloadplus/link_extension');
        $this->_setResourceModel('downloadplus/link_extension', 'downloadplus/link_extension_collection');
    }

    /*
     * Initializes specific Data
     */
    public function initialize()
    {
    	Mage::getModel('downloadplus/config');
   	
    	$this->setData('id', null);
    	$this->setData('link_id', null);
    	$this->setData('expiry', null);
    	$this->setData('expire_on', self::EXPIRE_ON_NEVER);
    	$this->setData('serial_number_pool', Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE);
    	$this->setData('attributes', null);
    	
    	return $this;
    }

    /*
     * Returns flag if link expires
     */
    public function doesExpire()
    {
    	if (!is_null($this->getExpiry())) {
    		$result = $this->getExpireOn()!=self::EXPIRE_ON_NEVER;
    	} else {
    		$result = false;
    	}
    	return $result;
    }
    
    /*
     * Returns flag if link does never expire
     */
    public function isExpireNever()
    {
    	if (!is_null($this->getExpiry())) {
    		$result = $this->getExpireOn()==self::EXPIRE_ON_NEVER;
    	} else {
    		$result = null;
    	}
    	return $result;
    }
    
    /*
     * Returns flag if link starts to expire on order
     */
    public function isExpireOnOrder()
    {
    	if (!is_null($this->getExpiry())) {
    		$result = $this->getExpireOn()==self::EXPIRE_ON_ORDER;
    	} else {
    		$result = null;
    	}
    	return $result;
    }

    /*
     * Returns flag if link starts to expire on first download
     */
    public function isExpireOnDownload()
    {
    	if (!is_null($this->getExpiry())) {
    		$result = $this->getExpireOn()==self::EXPIRE_ON_DOWNLOAD;
    	} else {
    		$result = null;
    	}
    	return $result;
    }
    
    /*
     * Loads the extension by Link
     */
    public function loadByLink($link)
    {
    	$this->initialize();
    	 
    	if ($link instanceof Mage_Downloadable_Model_Link) {
    		$this->loadByLinkId($link->getId());
    	}
    	if (is_numeric($link)) {
    		$this->loadByLinkId($link);
    	}
    	return $this;
    }

    /*
     * Loads the extension by LinkId
     */
    public function loadByLinkId($id)
    {
    	$this->initialize();
    	$this->setLinkId($id);
    	 
    	if ($id) {
	        if ($result = $this->getResource()->getIdByLinkId($id)) {
	        	$this->load($result);
	        }
    	}
    	return $this;
    }

    /*
     * Override get function
     */
    public function getExpiry()
    {
    	if ($this->getData('expiry')==0) {
    		$this->setData('expiry', null);
    	}
    	return $this->getData('expiry');
    }

    /*
     * Override get function
     */
    public function getExpireOn()
    {
    	if (is_null($this->getExpiry())) {
    		$this->setData('expire_on', self::EXPIRE_ON_NEVER);
    	}
    	return $this->getData('expire_on');
    }

    /*
     * Get related serialnumber pools
     */
    public function getSerialnumberPools()
    {
    	$result = $this->getResource()->getSerialnumberPools();
    	return $result;
    }

    /*
     * Return bool if serialnumber pool is set
     */
    public function hasSerialNumberPool()
    {
    	$config = Mage::getModel('downloadplus/config');
    	$result = (!is_null($this->getSerialNumberPool()) && $this->getSerialNumberPool()!=false);
    	return $result;
    }

    /*
     * Returns bool if serialnumbers are used
     */
    public function hasSerialnumbers()
    {
    	$result = ($this->getSerialNumberPool()!=false);
    	return $result;
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
    
    public function setAttribute($code, $value)
    {
    	$attributes = unserialize($this->getData('attributes'));
   		$attributes[$code] = $value;
   		$this->setData('attributes', serialize($attributes));
   		
    	return $this;
    }
    
    public function getAttribute($code, $default=null)
    {
    	$result = null;
    	$attributes = $this->getAttributes();
    	if (isset($attributes[$code])) {
    		$result = $attributes[$code];
    	} else {
    		$result = $default;
    	}
    	
    	return $result;
    }

    public function getAttributeFrontendValue($code) 
    {
    	$result = null;
    	if ($attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $code)) {
    		$object = new Varien_Object();
    		$object->setData($code, $this->getAttribute($code));
    		$result = $attribute->getFrontend()->getValue($object);
    	}
    	return $result;
    }
    
    public function setAttributes($attributes)
    {
    	if ($attributes) {
    		$this->setData('attributes', serialize($attributes));
    	} else {
    		$this->setData('attributes', null);
    	}
    	return $this;
    }
    
    public function getAttributes()
    {
    	$result = Array();
    	if ($this->getData('attributes')) {
    		$result = unserialize($this->getData('attributes'));
    	}
    	return $result;
    }

    /*
     * Saves the Data of the Model and the related Models
     */
    public function save()
    {
    	if (is_null($this->getExpiry()) && $this->getExpireOn()!=self::EXPIRE_ON_NEVER) {
    		$this->setExpireOn(self::EXPIRE_ON_NEVER);
    	}
    	if ($this->getExpireOn()==self::EXPIRE_ON_NEVER) {
    		$this->setExpiry(null);
    	}
    	parent::save();
    	return $this;
    }

    /*
     * Deletes the Data of this Model and the related Models
     */
    public function delete()
    {
    	parent::delete();
       	return $this;
    }

}
