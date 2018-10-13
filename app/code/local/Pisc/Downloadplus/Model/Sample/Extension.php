<?php
/**
 * Downloadplus Sample Extension Model
*
* @author     PILLWAX Industrial Solutions Consulting
* @category   Pisc
* @package    Pisc_Downloadplus
* @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
* @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
* @version    0.1.0
*/

class Pisc_Downloadplus_Model_Sample_Extension extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_sample_extension';
	protected $_sample = null;
	
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        //parent::_construct();
        $this->_init('downloadplus/sample_extension');
        $this->_setResourceModel('downloadplus/sample_extension', 'downloadplus/sample_extension_collection');
    }

    /*
     * Initializes specific Data
     */
    public function initialize()
    {
    	Mage::getModel('downloadplus/config');
   	
    	$this->setData('id', null);
    	$this->setData('sample_id', null);
    	$this->setData('attributes', null);
    	
    	return $this;
    }
    
    /*
     * Loads the extension by Link
     */
    public function loadBySample($sample)
    {
    	$this->initialize();
    	 
    	if ($link instanceof Mage_Downloadable_Model_Sample) {
    		$this->loadBySampleId($link->getId());
    	}
    	if (is_numeric($sample)) {
    		$this->loadBySampleId($sample);
    	}
    	return $this;
    }

    /*
     * Loads the extension by LinkId
     */
    public function loadBySampleId($id)
    {
    	$this->initialize();
    	$this->setSampleId($id);
    	 
    	if ($id) {
	        if ($result = $this->getResource()->getIdBySampleId($id)) {
	        	$this->load($result);
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
    		$value = $this->getAttribute($code);
    		if (is_array($value)) {
    			$value = implode(',', $value);
    		}
    		$object->setData($code, $value);
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
    	$attributes = Array();
    	if ($this->getData('attributes')) {
    		$attributes = unserialize($this->getData('attributes'));
    	}
    		
   		// Set default values
   		$link = $this->getLink();
   		if ($link->getProductId()) {
   			$product = Mage::getModel('catalog/product')->load($link->getProductId());
   			$availableAttributes = Mage::helper('downloadplus')->getCustomDownloadableAttributes($product, 'sample');
   			
   			foreach ($availableAttributes as $code=>$availableAttribute) {
   				if (!isset($attributes[$code])) {
   					$attributes[$code] = $availableAttribute->getDefaultValue();
   				}
    		}
   		}
   		
    	return $attributes;
    }

    public function getSample()
    {
    	if (!$this->_sample) {
    		$this->_sample = Mage::getModel('downloadplus/sample');
    		if ($this->getSampleId()) {
    			$this->_sample->load($this->getSampleId());
    		}
    	}
    	return $this->_sample;
    }
    
    /*
     * Saves the Data of the Model and the related Models
     */
    public function save()
    {
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
