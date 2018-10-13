<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloadable Link model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.5
 */

class Pisc_Downloadplus_Model_Link extends Mage_Downloadable_Model_Link
{
	
	protected $_eventPrefix = 'downloadplus_link';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('downloadplus/link');
    }

    public static function getBasePath($type=null)
    {
    	Mage::helper('downloadplus/download');
    	if (Mage::helper('downloadplus')->existsDownloadplusFile() && $type==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
    		$config = Mage::getModel('downloadplusfile/config')->setStore();
    		return $config->getLocalFilePath();
    	}
    	return parent::getBasePath();
    }

    public static function getBaseSamplePath($type=null)
    {
    	Mage::helper('downloadplus/download');
    	if (Mage::helper('downloadplus')->existsDownloadplusFile() && $type==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
    		$config = Mage::getModel('downloadplusfile/config')->setStore();
    		return $config->getLocalFilePath();
    	}
    	return parent::getBaseSamplePath();
    }
    
    /*
     * Returns the Link Title
     */
    public function getLinkTitle()
    {
    	$result = null;

        $sql = $this->_getResource()->getReadConnection()->select()
	            	->from($this->_getResource()->getTable('downloadable/link_title'))
	            	->where('link_id = ?', $this->getLinkId())
	            	->where('store_id = ?', $this->getStoreId());

        if ($result = $this->_getResource()->getReadConnection()->fetchRow($sql)) {
        	return $result['title'];
        }

        return null;
    }

    /*
     * Returns link price
     */
    public function getPrice()
    {
    	$result = null;
    	$websiteId = Mage::getModel('core/store')->load($this->getStoreId());
    
    	$sql = $this->_getResource()->getReadConnection()->select()
    				->from($this->_getResource()->getTable('downloadable/link_price'))
    				->where('link_id = ?', $this->getLinkId())
    				->where('website_id = ? OR website_id = 0', $websiteId->getId());
    
    	if ($result = $this->_getResource()->getReadConnection()->fetchRow($sql)) {
    		return $result['price'];
    	}
    
    	return null;
    }
    
    /*
     * Returns a Id by Filename
     */
    public function getIdByFile($file)
    {
    	$result = null;

    	if (!empty($file)) {
	        $sql = $this->_getResource()->getReadConnection()->select()
		            	->from($this->_getResource()->getTable('downloadable/link'))
		            	->where("link_file='".$file."'");

	        if ($result = $this->_getResource()->getReadConnection()->fetchOne($sql)) {
	        	return $result;
	        }
    	}

        return null;
    }

    /*
     * Returns a Id by Sample Filename
     */
    public function getIdBySampleFile($file)
    {
    	$result = null;

    	if (!empty($file)) {
	        $sql = $this->_getResource()->getReadConnection()->select()
		            	->from($this->_getResource()->getTable('downloadable/link'))
		            	->where("sample_file='".$file."'");

	        if ($result = $this->_getResource()->getReadConnection()->fetchOne($sql)) {
	        	return $result;
	        }
    	}

        return null;
    }

    public function getAttributes()
    {
    	$product = Mage::getModel('catalog/product')->load($this->getProductId());
    	$attributes = Mage::app()->getHelper('downloadplus')->getCustomDownloadableAttributes($product, 'link');
    	$extension = Mage::getModel('downloadplus/link_extension')->loadByLink($this);
    	
    	foreach ($attributes as $attribute) {
    		$attribute->setValue($extension->getAttribute($attribute->getAttributeCode()));
    	}
    	
    	return $attributes;
    	 
    }

    /*
     * Returns related extension
     */
    public function getExtension()
    {
    	$result = Mage::getModel('downloadplus/link_extension');
    	if ($this->getId()) {
    		$result->load($this->getId(), 'link_id');
    		if (is_null($result->getId())) {
    			$result->setLinkId($this->getId());
    		}
    	}
    	return $result;
    }
    
}
