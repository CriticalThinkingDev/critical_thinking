<?php
/**
 * Downloadplus Sample Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusMagazine
 * @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.3
 */

class Pisc_Downloadplus_Model_Sample extends Mage_Downloadable_Model_Sample
{

	protected $_eventPrefix = 'downloadplus_sample';
	protected $_extension = null;
	
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('downloadplus/sample');
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
    
    /*
     * Returns a Id by Filename
     */
    public function getIdByFile($file)
    {
    	$result = null;

    	if (!empty($file)) {
	        $sql = $this->_getResource()->getReadConnection()->select()
		            	->from($this->_getResource()->getTable('downloadable/sample'))
		            	->where("sample_file='".$file."'");

	        if ($result = $this->_getResource()->getReadConnection()->fetchOne($sql)) {
	        	return $result;
	        }
    	}

        return null;
    }

    /*
     * Returns the Sample Title
    */
    public function getSampleTitle()
    {
    	$result = null;
    
    	$sql = $this->_getResource()->getReadConnection()->select()
		    	->from($this->_getResource()->getTable('downloadable/sample_title'))
		    	->where('sample_id = ?', $this->getSampleId())
		    	->where('store_id = ?', $this->getStoreId());
    
    	if ($result = $this->_getResource()->getReadConnection()->fetchRow($sql)) {
    		return $result['title'];
    	}
    
    	return null;
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

    public function getAttributes()
    {
    	$product = Mage::getModel('catalog/product')->load($this->getProductId());
    	$attributes = Mage::app()->getHelper('downloadplus')->getCustomDownloadableAttributes($product, 'sample');
    	$extension = $this->getExtension();
    	 
    	foreach ($attributes as $attribute) {
    		$attribute->setValue($extension->getAttribute($attribute->getAttributeCode()));
    	}
    	 
    	return $attributes;
    }
    
    public function getAttribute($code, $default=null)
    {
    	$result = null;
    	$attributes = $this->getAttributes();
    	if (isset($attributes[$code])) {
    		$result = $attributes[$code]->getValue();
    	} else {
    		$result = $default;
    	}
    
    	return $result;
    }
    
    public function getImageFile()
    {
    	return $this->getExtension()->getImageFile();
    }
    
    public function setImageFile($file)
    {
    	return $this->getExtension()->setImageFile($file);
    }
    
    public function getImageUrl($type=null)
    {
    	if ($type=='thumbnail') {
    		return Mage::getModel('downloadplus/sample_image')->setSample($this)->getImageThumbnailUrl();
    	}
    	return Mage::getModel('downloadplus/sample_image')->setSample($this)->getImageUrl();
    }
    
    protected function _getImageHelper()
    {
    	return Mage::helper('downloadplus/sample_image');
    }
    
    public function getThumbnailUrl($width = null, $height = null)
    {
    	if (empty($width) || empty($height)) {
    		$width = 64;
    		$height = 64;
    	}
    
    	$url = null;
    	$file = $this->getImageFile();
    	if (!empty($file) && $this->getProductId()) {
    		$product = Mage::getModel('catalog/product')->load($this->getProductId());
    		$url = (string)$this->_getImageHelper()
		    		->init($product, 'thumbnail', $file)
    				->resize($width, $height);
    	}
    	return $url;
    }
    
    public function updateImageFileFromUpload($source, $destination, $product=null)
    {
    	if (empty($destination) || empty($source)) {
    		return false;
    	}
    	if (!$product) {
    		$product = Mage::getModel('catalog/product')->load($this->getProductId());
    	}
    	$sourceFile = Mage::getSingleton('downloadplus/sample_image')->getBaseTmpPath().DS.$source;
    	$destFile = Mage::getSingleton('downloadplus/sample_image')->getBasePath($product).DS.$destination;
    
    	if (file_exists($sourceFile)) {
    		if (file_exists($destFile)) {
    			@unlink($destFile);
    		}
    		try {
    			mkdir(Mage::getSingleton('downloadplus/sample_image')->getBasePath($product), 0770, true);
    			rename($sourceFile, $destFile);
    		} catch (Exception $e) {
    			Mage::log('Error occured while moving uploaded file from %s to $s: $s', $sourceFile, $destFile, $e->getMessage());
    		}
    		if (file_exists($destFile)) {
    			@chmod($destFile, 0770);
    			$this->setImageFile('/'.$product->getId().'/'.$destination);
    			return true;
    		}
    	}
    
    	return false;
    }
    
    /*
     * Returns related extension
     */
    public function getExtension()
    {
    	if (!$this->_extension) {
    		$this->_extension = Mage::getModel('downloadplus/sample_extension');
    		if ($this->getId()) {
    			$this->_extension->load($this->getId(), 'sample_id');
    			if (is_null($this->_extension->getId())) {
    				$this->_extension->setLinkId($this->getId());
    			}
    		}
    	}
    	return $this->_extension;
    }
    
    public function save()
    {
    	parent::save();
    	if ($this->_extension) {
    		if ($this->getId() && !$this->_extension->getId()) {
    			$this->_extension->setSampleId($this->getId());
    		}
    		$this->_extension->save();
    	}
    	return $this;
    }
    
}
