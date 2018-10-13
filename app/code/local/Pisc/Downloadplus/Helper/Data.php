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
 * @version    0.1.18
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
	 * Returns boolean if the Link is set to receive Serialnumbers
	 */
	public function assignSerialnumberToLink($linkId)
	{
        $extension = Mage::getModel('downloadplus/link_extension')->loadByLinkId($linkId);
        return $extension->hasSerialnumbers();
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
		$_product = null;
		if ($product instanceof Mage_Catalog_Model_Product) {
		    $_product = $product;
		} elseif (is_numeric($product)) {
		    $_product = Mage::getModel('catalog/product')->load($product);
		}
		if ($_product) {
		    if ($_product->getTypeId()==Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
		        $links = Mage::getModel('downloadable/product_type')->getLinks($_product);
		        if ($links) {
		            $linkIds = array_keys($links);
		            $result = Mage::getModel('downloadplus/link_extension')->getResource()
		                                ->clearFilter()
                    		            ->addLinkIdsToFilter($linkIds)->getSerialnumberPools();
		        
		            $pools = Mage::getModel('downloadplus/product_serialnumber')->getResource()
                    		            ->clearFilter()
                    		            ->addProductToFilter($_product)->getSerialnumberPools();
		        
		            foreach($pools as $pool) {
		                if (!empty($pool) && !in_array($pool, $result)) {
		                    $result[] = $pool;
		                }
		            }
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

		if ($product instanceof Mage_Catalog_Model_Product && $product->getTypeId()) {
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

	public function getLinkAttributes($link)
	{
		$attributes = Array();
		if ($link instanceof Mage_Downloadable_Model_Link) {
			$attributes = Mage::getModel('downloadplus/link')->load($link->getLinkId())->getAttributes();
		}
		if ($link instanceof Pisc_Downloadplus_Model_Link_Product_Item) {
			$attributes = Mage::getModel('downloadplus/link_product_item')->load($link->getLinkId())->getAttributes();
		}
		if ($link instanceof Mage_Downloadable_Model_Link_Purchased_Item) {
			$attributes = Mage::getModel('downloadplus/link_purchased_item')->load($link->getId())->getAttributes();
		}
		if ($link instanceof Pisc_DownloadplusBonus_Model_Link_Bonus_Item) {
			$attributes = Mage::getModel('downloadplusbonus/link_bonus_item')->load($link->getId())->getAttributes();
		}

		return $attributes;
	}

	public function getLinkAttributesHtml($link)
	{
		$html = '';
		$attributes = $this->getLinkAttributesData($link);
		foreach ($attributes as $attribute) {
			$html.= '<li class="'.$attribute['code'].'"><span class="label">'.$attribute['label'].':</span><span class="value">'.$attribute['value'].'</span></li>';
		}
		if ($html) {
			$html = '<ul class="link-attributes">'.$html.'</ul>';
		}
		return $html;
	}

	public function getLinkAttributesData($link, Array $excludeAttr = Array())
	{
		$data = array();
		$attributes = $this->getLinkAttributes($link);
		foreach ($attributes as $attribute) {
			$value = $attribute->getValue();
			if (!empty($value) && $attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
				$object = new Varien_Object();
				if (is_array($attribute->getValue())) {
					$object->setData($attribute->getAttributeCode(), implode(',', $attribute->getValue()));
				} else {
					$object->setData($attribute->getAttributeCode(), $attribute->getValue());
				}
				$value = $attribute->getFrontend()->getValue($object);

				if (is_string($value) && strlen($value)) {
					if ($attribute->getFrontendInput() == 'price') {
						$value = Mage::app()->getStore()->convertPrice($value,true);
					} elseif (!$attribute->getIsHtmlAllowedOnFront()) {
						$value = $this->htmlEscape($value);
					}
					$data[$attribute->getAttributeCode()] = array(
							'label' => $attribute->getFrontend()->getLabel(),
							'value' => $value,
							'code'  => $attribute->getAttributeCode()
					);
				}
			}
		}
		return $data;
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

		// Global Pool Name shall not be used by Product
		if (is_null($product)) {
		    if ((is_array($serialPool) && empty($serialPool['new']) && empty($serialPool['use'])) || (is_string($serialPool) && empty($serialPool))) {
		        Mage::throwException($this->__('Please use a Global Pool name.'));
		    }

		    if (is_string($serialPool)) {
		        $serialPool = Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL.$serialPool;
		    } elseif (is_array($serialPool) && !empty($serialPool['new']) && strpos($serialPool['new'], Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL)!==0) {
		        $serialPool['new'] = Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL.$serialPool['new'];
		    }

		    $collection = Mage::getModel('downloadplus/product_serialnumber')->getCollection()
		                     ->addFieldToFilter('product_id', Array('neq'=>'NULL'));
		    if (is_array($serialPool)) {
		        $collection->addFieldToFilter('serial_number_pool', Array('eq'=>$serialPool['new']));
		    } else {
		        $collection->addFieldToFilter('serial_number_pool', Array('eq'=>$serialPool));
		    }

			if ($collection->getSize()>0) {
				Mage::throwException($this->__('Serialnumberpool "%s" is already used by a Product. Please choose a different Pool Name.', $serialPool));
			}
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

				if (is_array($serialPool)) {
					if (isset($serialPool['new']) && !empty($serialPool['new'])) {
					    if (is_null($product) && strpos($serialPool['new'], Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL)!==0) {
					        $serialPool['new'] = Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL.$serialPool['new'];
					    }
						$serialNumber->setSerialNumberPool($serialPool['new']);
					} elseif (isset($serialPool['use']) && !empty($serialPool['use'])) {
						$serialNumber->setSerialNumberPool($serialPool['use']);
					} elseif (!is_null($product)) {
					    $serialNumber->setSerialNumberPool(Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT);
					}
				} else {
				    $serialNumber->setSerialNumberPool($serialPool);
				}

				if (!is_null($serialNumber->getData('serial_number_pool')) && !$serialNumber->exists()) {
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

	/*
	 * Returns if DownloadPlus Editionguard Extension is active
	 */
	public function existsDownloadplusEditionguard()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusEditionguard')) {
			$result = $module->is('active');
		}
		return $result;
	}

	/*
	 * Returns if DownloadPlus Builder Extension is active
	 */
	public function existsDownloadplusBuilder()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusBuilder')) {
			$result = $module->is('active');
		}
		return $result;
	}

	/*
	 * Returns if DownloadPlus Associated Extension is active
	 */
	public function existsDownloadplusAssociated()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusAssociated')) {
			$result = $module->is('active');
		}
		return $result;
	}

	/*
	 * Returns if DownloadPlus Magazine Extension is active
	 */
	public function existsDownloadplusMagazine()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusMagazine')) {
			$result = $module->is('active');
		}
		return $result;
	}

	/*
	 * Returns if DownloadPlus Bonus Extension is active
	 */
	public function existsDownloadplusBonus()
	{
		$result = false;
		if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusBonus')) {
			$result = $module->is('active');
		}
		return $result;
	}

	/*
	 * Returns if DownloadPlus Bonus Extension is active
	 */
	public function existsDownloadplusRepository()
	{
	    $result = false;
	    if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusRepository')) {
	        $result = $module->is('active');
	    }
	    return $result;
	}

	/*
	 * Returns if DownloadPlus Options Extension is active
	 */
	public function existsDownloadplusOptions()
	{
	    $result = false;
	    if ($module = Mage::getConfig()->getNode('modules/Pisc_DownloadplusOptions')) {
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

	public function getAppEmulation()
	{
		$emulation = null;
		if (Mage::helper('downloadplus/magento')->isVersion('1.5')) {
			$emulation = Mage::getSingleton('core/app_emulation');
		}

		return $emulation;
	}

	/*
	 * Returns if resumable download is configured
	 */
	public function isDownloadResumeable()
	{
		$config = Mage::getModel('downloadplus/config')->setStore($this->getStore());
		$result = $config->getDownloadableDeliveryResumeable()==Pisc_Downloadplus_Model_Config::CONFIG_DOWNLOAD_RESUME_ON;
		return $result;
	}

	/*
	 * Saves data in current session
	 */
	public function saveInDownloadSession($key, $value)
	{
		$session = Mage::getSingleton('core/session');
		$object = $session->getDownloadplusDownloadSession();
		if (!$object) {
			$object = new Varien_Object();
		}
		$object->setData($key, $value);
		return $session->setDownloadplusDownloadSession($object);
	}

	/*
	 * Gets data from current session
	 */
	public function getFromDownloadSession($key)
	{
		$session = Mage::getSingleton('core/session');
		$result = false;
		if ($object = $session->getDownloadplusDownloadSession()) {
			$result = $object->getData($key);
		}
		return $result;
	}

	/*
	 * Clears session storage
	 */
	public function clearDownloadSession()
	{
		$session = Mage::getSingleton('core/session');
		$session->setDownloadplusDownloadSession(new Varien_Object());
		return $this;
	}

	/*
	 * Returns the status for a Link from Order Item
	 */
	public function getLinkPurchasedItemStatus($purchasedLink, $item)
	{
	    $order = $item->getOrder();

	    $status = $purchasedLink->getStatus();

	    $linkStatuses = array(
	        'pending'         => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING,
	        'expired'         => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED,
	        'avail'           => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE,
	        'payment_pending' => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
	        'payment_review'  => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
	    );

	    $orderItemStatusToEnable = Mage::getStoreConfig(
	        Mage_Downloadable_Model_Link_Purchased_Item::XML_PATH_ORDER_ITEM_STATUS, $order->getStoreId()
	    );

	    if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
	        $status = $linkStatuses['pending'];
	    } elseif ($order->isCanceled()
	        || $order->getState() == Mage_Sales_Model_Order::STATE_CLOSED
	        || $order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE
	    ) {
	        $expiredStatuses = Array(
	            Mage_Sales_Model_Order_Item::STATUS_CANCELED,
	            Mage_Sales_Model_Order_Item::STATUS_REFUNDED,
	        );
            if ($item->getProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                || $item->getRealProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            ) {
                if (in_array($item->getStatusId(), $expiredStatuses)) {
                    $status = $linkStatuses['expired'];
                } else {
                    $status = $linkStatuses['avail'];
                }
            }
	    } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
	        $status = $linkStatuses['payment_pending'];
	    } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW) {
	        $status = $linkStatuses['payment_review'];
	    } else {
	        $availableStatuses = Array(
	            $orderItemStatusToEnable,
	            Mage_Sales_Model_Order_Item::STATUS_INVOICED
            );
            if ($item->getProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                || $item->getRealProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            ) {
                if ($item->getStatusId() == Mage_Sales_Model_Order_Item::STATUS_BACKORDERED &&
                    $orderItemStatusToEnable == Mage_Sales_Model_Order_Item::STATUS_PENDING &&
                    !in_array(Mage_Sales_Model_Order_Item::STATUS_BACKORDERED, $availableStatuses, true) ) {
                        $availableStatuses[] = Mage_Sales_Model_Order_Item::STATUS_BACKORDERED;
                    }

                if (in_array($item->getStatusId(), $availableStatuses)) {
                    $status = $linkStatuses['avail'];
                }
            }
	    }

        return $status;
	}

	public function isFlashUploader()
	{
	    return !class_exists('Mage_Uploader_Block_Abstract');
	}

}
