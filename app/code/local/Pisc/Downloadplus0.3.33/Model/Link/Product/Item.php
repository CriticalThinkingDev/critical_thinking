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
 * @version		0.1.1
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
    	if (is_null($this->_downloadDetail)) {
    		$this->_downloadDetail = Mage::getModel('downloadplus/download_detail');
	    	if ($id = $this->getId()) {
	    		$this->_downloadDetail->loadByLinkProductItemId($id);
	    	} else {
	    		$this->_downloadDetail->create($this->getLinkFile(), Mage::getModel('downloadplus/product_download')->getBasePath());
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

    /*
     * Loads the Data of the Model and the related Models
     */
    public function load($id, $field=null)
    {
    	parent::load($id, $field);

    	$this->getDownloadDetail();
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
    	$this->getDownloadDetail()->setLinkProductItemId($this->getId());
    	$this->getDownloadDetail()->save();

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
    	parent::delete();

       	return $this;
    }

}
