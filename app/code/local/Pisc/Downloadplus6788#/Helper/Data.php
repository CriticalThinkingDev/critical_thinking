<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license	   Commercial Unlimited License
 */

/**
 * Downloadplus data helper
 *
 * @category   Pisc
 * @package    Mage_Downloadplus
 * @author
 * @version    0.1.8
 */

class Pisc_Downloadplus_Helper_Data extends Mage_Core_Helper_Abstract
{

	const CUSTOM_ATTRIBUTE_PREFIX = 'downloadable_';
	
	/*
	 * Returns a Byte value in Human Readable Format
	 */
	public function getBytesFormatted($bytes, $precision=2)
	{
		$units = array('Bytes', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	public function getDownloadableLinkIdByFile($file)
	{
		$resource = Mage::getModel('downloadable/link')->getResource();
        $sql = $resource->getReadConnection()
        			->select()
        			->from($resource->getTable('downloadable/link'))
        			->where("link_file='".$file."'");

        $result = $resource->getReadConnection()->fetchOne($sql);
        return $result;
	}

	public function getDownloadableLinkSampleIdByFile($file)
	{
		$resource = Mage::getModel('downloadable/link')->getResource();
        $sql = $resource->getReadConnection()
        			->select()
		   			->from($resource->getTable('downloadable/link'))
        			->where("sample_file='".$file."'");

        $result = $resource->getReadConnection()->fetchOne($sql);
        return $result;
	}

	public function getDownloadableSampleIdByFile($file)
	{
		$resource = Mage::getModel('downloadable/sample')->getResource();
        $sql = $resource->getReadConnection()
        			->select()
		   			->from($resource->getTable('downloadable/sample'))
        			->where("sample_file='".$file."'");

        $result = $resource->getReadConnection()->fetchOne($sql);
        return $result;
	}

	/*
	 * Returns true if a Product has serialnumbers assigned
	 */
	public function hasSerialnumbers($product, $link=null)
	{
		$result = false;
		$count = 0;
		$config = Mage::getModel('downloadplus/config');

		$id = null;
		$linkId = null;
		if ($product instanceof Mage_Catalog_Model_Product) {
			$productId = $product->getId();
		} else {
			$productId = $product;
		}
		if (is_object($link)) {
			$linkId = $link->getId();
		} else {
			$linkId = $link;
		}

		// Check if a link has a serialnumber pool associated
		if ($linkId) {
			$extension = Mage::getModel('downloadplus/link_extension')->loadByLinkId($linkId);
			/*
			Mage::log('>>> Has serialnumbers > Link Extension Data:', null, 'downloadplus.log');
			Mage::log($extension->getData(), null, 'downloadplus.log');
			*/

			if ($extension->hasSerialnumbers()) {
				$count = Mage::getModel('downloadplus/product_serialnumber')->getCountByLink($linkId);
			}
		} else {
			/*
			Mage::log('>>> Has serialnumbers > Product Data:', null, 'downloadplus.log');
			Mage::log(Mage::getModel('catalog/product')->load($productId)->getData(), null, 'downloadplus.log');
			*/

			$count = Mage::getModel('downloadplus/product_serialnumber')->getCountByProduct($productId);
		}
	
		// Check if there has been serialnumbers assigned for this product
		$resource = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->getResource();
    	$sql = $resource->getReadConnection()
    			->select()
				->from($resource->getTable('downloadplus/link_purchased_item_serialnumber'));
    	
		if ($productId) {
			$sql->where("product_id=?", $productId);
    	}
    	if ($linkId) {
    		$sql->where("link_id=?", $linkId);
    	}
	    	
		if ($items = $resource->getReadConnection()->fetchAll($sql)) {
			$count = $count + count($items);
		}
		$result = ($count>0);

		/*
		Mage::log('>>> Has serialnumbers: Count='.$count, null, 'downloadplus.log');
		*/
		
		return $result;
	}
	
	/*
	 * Returns if a product has serialnumbers forced to be deactivated
	 */
	public function hasSerialnumbersDeactivated($product)
	{
		$result = false;
		
		if (!($product instanceof Mage_Catalog_Model_Product)) {
			$product = Mage::getModel('catalog/product')->load($product);
		}
		
		$result = strtolower($product->getAttributeText('downloadplus_serialnr_inactive'))=='yes';
		
		return $result;
	}

	/*
	 * Returns number of available serialnumbers for product
	 */
	public function getSerialnumberAvailableCount($product, $link=null)
	{
		$count = 0;
		$config = Mage::getModel('downloadplus/config');
		
		if ($product instanceof Mage_Catalog_Model_Product) {
			$productId = $product->getId();
		} else {
			$productId = $product;
		}

		$count = Mage::getModel('downloadplus/product_serialnumber')->getCountByProduct($productId);
		
		return $count;
	}
	
	/*
	* Returns known serialnumber global pools
	*/
	public function getSerialnumberPoolsGlobal()
	{
		$result = Array();
		
		$pools = Mage::getModel('downloadplus/product_serialnumber')->getResource()
					->clearFilter()
					->addGlobalToFilter()->getSerialnumberPools();
		
		foreach($pools as $pool) {
			if (!empty($pool) && !in_array($pool, $result)) {
				$result[] = $pool;
			}
		}
		
		return $result;
	}
	
	/*
	 * Returns known serialnumber pools for product
	 */
	public function getSerialnumberPoolsByProduct($product)
	{
		$result = Array();

		// Get all Link IDs from the product
		if ($links = Mage::getModel('downloadable/product_type')->getLinks($product)) {
			$linkIds = array_keys($links);
			$result = Mage::getModel('downloadplus/link_extension')->getResource()
						->addLinkIdsToFilter($linkIds)->getSerialnumberPools();
			
			$pools = Mage::getModel('downloadplus/product_serialnumber')->getResource()
						->clearFilter()
						->addProductToFilter($product)->getSerialnumberPools();

			foreach($pools as $pool) {
				if (!empty($pool) && !in_array($pool, $result)) {
					$result[] = $pool;
				}
			}
		}
		
		return $result;
	}

	/*
	 * Returns array of custom "downloadable" attributes
	 */
	public function getCustomDownloadableAttributes($product, $type=null)
	{
		$prefix = self::CUSTOM_ATTRIBUTE_PREFIX;
		if ($type) { $prefix.= $type."_"; }
		
		$customAttributes = Array();
	
		if ($product instanceof Mage_Catalog_Model_Product) {
			// Attributes from Product
			$attributes = $product->getAttributes();
		} else {
			// All product attributes
			$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
			$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
								->setEntityTypeFilter($entityType->getId());
		}
		
		foreach ($attributes as $attribute) {
			if (substr($attribute->getAttributeCode(), 0, strlen($prefix))==$prefix) {
				$customAttributes[$attribute->getAttributeCode()] = $attribute; 
			}
		}
		
		return $customAttributes;
	}
	
	/*
	 * Returns if product has custom "downloadable" attributes
	 */
	public function hasCustomDownloadableAttributes($product, $type=null)
	{
		$result = $this->getCustomDownloadableAttributes($product, $type);
		$result = !empty($result);
		return $result;
	}

	public function getPostMaxSize()
	{
		return ini_get('post_max_size');
	}
	
	public function getUploadMaxSize()
	{
		return ini_get('upload_max_filesize');
	}
	
	public function getDataMaxSize()
	{
		return min($this->getPostMaxSize(), $this->getUploadMaxSize());
	}
	
	public function getDataMaxSizeInBytes()
	{
		$iniSize = $this->getDataMaxSize();
		$size = substr($iniSize, 0, strlen($iniSize)-1);
		$parsedSize = 0;
		switch (strtolower(substr($iniSize, strlen($iniSize)-1))) {
			case 't':
				$parsedSize = $size*(1024*1024*1024*1024);
				break;
			case 'g':
				$parsedSize = $size*(1024*1024*1024);
				break;
			case 'm':
				$parsedSize = $size*(1024*1024);
				break;
			case 'k':
				$parsedSize = $size*1024;
				break;
			case 'b':
			default:
				$parsedSize = $size;
				break;
		}
		return $parsedSize;
	}

	/*
	 * Import Serialnumbers
	 */
	public function importSerialnumbers($serials, $serialPool, $product=null)
	{
		if (!is_array($serials)) {
			$serials = str_replace("\r\n", "\n", strip_tags($serials));
			$serials = explode("\n", $serials);
		}
			
		$count = 0;
		foreach ($serials as $serial) {
			if (!empty($serial)) {
				// Do only create unique serialnumbers
				$serialNumber = Mage::getModel('downloadplus/product_serialnumber');
				$serialNumber->setSerialHash(null);
				if ($product instanceof Mage_Catalog_Model_Product) {
					$serialNumber->setProductId($product->getId());
				}
				$serialNumber->setSerialNumber($serial);
					
				if ($serialPool) {
					if (isset($serialPool['new']) && !empty($serialPool['new'])) {
						$serialNumber->setSerialNumberPool($serialPool['new']);
					} else {
						if (isset($serialPool['use']) && !empty($serialPool['use'])) {
							$serialNumber->setSerialNumberPool($serialPool['use']);
						}
					}
				}

				if (!$serialNumber->exists()) {
					$serialNumber->save();
					$count++;
				}
			}
		}
		
		return $count;
	}

	/*
	 * Saves data in current session
	*/
	public function saveInSession($key, $value)
	{
		$session = Mage::getSingleton('core/session');
		$object = $session->getDownloadplusSession();
		if (!$object) {
			$object = new Varien_Object();
		}
		if ($value instanceof Varien_Object) {
			$value = $value->getData();
		}
		$object->setData($key, $value);
		$session->setDownloadplusSession($object);
		return $this;
	}
	
	/*
	 * Gets data from current session
	*/
	public function getFromSession($key, $raw=false)
	{
		$session = Mage::getSingleton('core/session');
		$result = null;
		if ($object = $session->getDownloadplusSession()) {
			if ($data = $object->getData($key)) {
				$result = $data;
				if (is_array($data) && !$raw) {
					$result = new Varien_Object();
					$result->setData($data);
				}
			}
		}
		return $result;
	}
	
	/*
	 * Returns if DownloadPlus AWS Extension is active
	 */
	public function existsDownloadplusAWS()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusAWS')) {
			$result = $module->is('active');
		}
		return $result;
	}

	/*
	 * Returns if DownloadPlus File Extension is active
	 */
	public function existsDownloadplusFile()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusFile')) {
			$result = $module->is('active');
		}
		return $result;
	}

	/*
	 * Returns if DownloadPlus Email Extension is active
	 */
	public function existsDownloadplusEmail()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusEmail')) {
			$result = $module->is('active');
		}
		return $result;
	}

	/*
	 * Returns if DownloadPlus Code Extension is active
	*/
	public function existsDownloadplusCode()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusCode')) {
			$result = $module->is('active');
		}
		return $result;
	}
	
	public function getStore()
	{
		$result = null;
		if (Mage::app()->getStore()->isAdmin()) {
			$result = Mage::app()->getStore(Mage::app()->getRequest()->getParam('store', 0));
		} else {
			$result = Mage::app()->getStore();
		}
		return $result;
	}

	public function getStoreId()
	{
		if ($store = $this->getStore()) {
			return $store->getId();
		}
		return null;
	}

	public function isDownloadableFileLocal($file)
	{
		if ($this->existsDownloadplusFile()) {
			$config = Mage::getModel('downloadplusfile/config')->setStore($this->getStore());
			return file_exists($config->getLocalFilePath().$file);
		}
		return false;
	}
	
}
