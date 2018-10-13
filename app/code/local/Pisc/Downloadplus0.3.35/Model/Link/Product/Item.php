<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloadable Link Product Item model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Link_Product_Item extends Mage_Core_Model_Abstract
{

	protected $_eventPrefix = 'downloadplus_link_product_item';
	
	protected $_downloadDetail = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        //parent::_construct();
        $this->_init('downloadplus/link_product_item');
        $this->_setResourceModel('downloadplus/link_product_item', 'downloadplus/link_product_item_collection');
    }

    /*
     * Initializes specific Data
     */
    public function initialize()
    {
    	return $this;
    }

    /*
     * Returns a Id by Filename
     */
    public function getIdByFile($file)
    {
    	$result = null;

    	if (!empty($file)) {
	        $sql = $this->_getResource()->getReadConnection()->select()
		            	->from($this->_getResource()->getTable('downloadplus/link_product_item'))
		            	->where("link_file='".$file."'");

	        if ($result = $this->_getResource()->getReadConnection()->fetchOne($sql)) {
	        	return $result;
	        }
    	}

        return null;
    }

    /*
     * Returns the related Download Detail
     */
    public function getDownloadDetail()
    {
    	if (!$this->_downloadDetail) {
    		$this->_downloadDetail = Mage::getModel('downloadplus/download_detail');
	    	if ($id = $this->getId()) {
	    		$this->_downloadDetail->loadByLinkProductItemId($id, $this->getStoreId());
	    	} else {
	    		$this->_downloadDetail->create($this->getLinkFile(), Mage::getModel('downloadplus/product_download')->getBasePath());
	    	}
	    	if ($this->_downloadDetail->getStoreId()) {
	    		$this->setStoreDescription($this->_downloadDetail->getDetail());
	    	}
    	}
    	return $this->_downloadDetail;
    }

    /*
     * Returns the related Product
     */
    public function getProduct()
    {
   		$product = Mage::getModel('catalog/product');
   		if ($id = $this->getProductId()) {
   			$product->load($id);
   		}
   		return $product;
    }

    public function getStoreId()
    {
    	if (is_null($this->getData('store_id'))) {
    		$this->setData('store_id', 0);
    	}
    	return $this->getData('store_id');
    }
    
    /*
     * Moves the uploaded Link File to its location and sets link file data
     */
    public function updateLinkFile($source, $destination, $product)
    {
    	$sourceFile = Mage::getModel('downloadplus/product_download')->getBaseTmpPath($product).DS.$source;
    	$destFile = Mage::getModel('downloadplus/product_download')->getBasePath($product).DS.$destination;

    	if (file_exists($sourceFile)) {
    		if (file_exists($destFile)) {
    			@unlink($destFile);
    		}
    		@rename($sourceFile, $destFile);
    		if (file_exists($destFile)) {
    			@chmod($destFile, 0770);
    			$this->setLinkFile('/'.$product->getId().'/'.$destination);
    		}
    	}

    	return $this;
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
    	$attributes = unserialize($this->getData('attributes'));
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
    	
    public function getAttributes($raw=false)
    {
    	$attributes = Array();
    	if ($raw) {
    		if ($this->getData('attributes')) {
    			$attributes = unserialize($this->getData('attributes'));
    		}
    	} else {
	    	$product = Mage::getModel('catalog/product')->load($this->getProductId());
	    	$attributes = Mage::app()->getHelper('downloadplus')->getCustomDownloadableAttributes($product, 'link');
	    		 
	    	foreach ($attributes as $attribute) {
	    		$attribute->setValue($this->getAttribute($attribute->getAttributeCode()));
	    	}
    	}
    	
    	return $attributes;
    }

    /*
   	public function getAttributes()
   	{
    	$result = Array();
    	if ($this->getData('attributes')) {
    		$result = unserialize($this->getData('attributes'));
    	}
    	return $result;
    }
    */

    /*
     * Returns the Link Title
    */
    public function getLinkTitle()
    {
    	$result = null;
    
    	$sql = $this->_getResource()->getReadConnection()->select()
    				->from($this->_getResource()->getTable('downloadplus/link_product_item_title'))
    				->where('link_id = ?', $this->getLinkId())
    				->where('store_id = ?', $this->getStoreId());
    
    	if ($result = $this->_getResource()->getReadConnection()->fetchRow($sql)) {
    		if ($result['store_id']) {
    			$this->setStoreTitle($result['title']);
    		}
    		return $result['title'];
    	} else {
    		$sql = $this->_getResource()->getReadConnection()->select()
    					->from($this->_getResource()->getTable('downloadplus/link_product_item_title'))
    					->where('link_id = ?', $this->getLinkId())
    					->where('store_id = ?', 0);
    		if ($result = $this->_getResource()->getReadConnection()->fetchRow($sql)) {
    			$this->setStoreTitle(null);
    			return $result['title'];
    		}
    	}
    
    	return null;
    }

    public function setDescription($text)
    {
    	$this->getDownloadDetail()->setDetail($text);
    	return $this;
    }

    public function getDescription()
    {
    	return $this->getDownloadDetail()->getDetail();
    }
    
    /*
     * Loads the Data of the Model and the related Models
     */
    public function load($id, $field=null)
    {
    	parent::load($id, $field);

    	// Lacy Load the Download Detail for Multi-Store support
    	/*$this->getDownloadDetail();*/
    	return $this;
    }

    /*
     * Saves the Data of the Model and the related Models
     */
    public function save()
    {
    	parent::save();

    	$this->getDownloadDetail()->makeActive();
    	$this->getDownloadDetail()->setProductId($this->getProductId());
    	$this->getDownloadDetail()->setStoreId($this->getStoreId());
    	$this->getDownloadDetail()->setLinkProductItemId($this->getId());
    	$this->getDownloadDetail()->save();
    	Mage::getModel('downloadplus/link_product_item_title')
    			->getResource()
    			->saveTitle($this);

    	return $this;
    }

    /*
     * Deletes the Data of this Model and the related Models
     */
    public function delete()
    {
    	if ($this->getDownloadDetail()->getId()) {
    		$this->getDownloadDetail()->delete();
    	}
    	if ($this->getLinkTitle()) {
    		$this->getResource()->deleteItems($this);
    	}
    	parent::delete();

       	return $this;
    }

}
