<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Product Download model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.1
 */

class Pisc_Downloadplus_Model_Product_Download extends Mage_Core_Model_Abstract
{

	protected $_eventPrefix = 'downloadplus_product_download';
	
	protected $_product = null;

    /**
     * Constructor
     *
     */
    public function _construct()
    {
        parent::_construct();
    }

    public function setProduct($product)
    {
    	$this->_product = $product;
    	return $this;
    }

    /**
     * Retrieve base temporary path
     *
     * @return string
     */
    public static function getBaseTmpPath($product=null)
    {
    	$result = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'product';
    	if ($product && $product->getId()) {
    		$result.= DS . $product->getId();
    	}
    	// Create the path if it not already exists
    	if (!file_exists($result)) {
    		@mkdir($result, 0770, true);
    	}
        return $result;
    }

    /**
     * Retrieve Base files path
     *
     * @return string
     */
    public static function getBasePath($product=null)
    {
    	$result = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'product' . DS . 'links';
    	if ($product && $product->getId()) {
    		$result.= DS . $product->getId();
    	}
    	// Create the path if it not already exists
    	if (!file_exists($result)) {
    		@mkdir($result, 0770, true);
    	}
    	return $result;
    }

}