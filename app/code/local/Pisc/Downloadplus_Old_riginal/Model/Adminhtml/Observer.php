<?php
/**
 * @category   Pisc
 * @package    Pisc_DownloadPlus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * DownloadPlus Adminhtml Event Observer
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.11
 */

class Pisc_Downloadplus_Model_Adminhtml_Observer
{

	/*
	 * Used to prevent circular events within this observer
	 */
	protected $_event = null;

	/*
	 * Returns true if in Admin Session
	 */
	public function isAdminSession()
	{
		$session = Mage::getSingleton('admin/session');
		if ($session) {
			return $session->isLoggedIn();
		}
		return false;
	}

	/*
	 * Get current StoreId
	 */
	public function getStoreId($observer)
	{
		return $observer->getRequest()->getParam('store', 0);
	}

	/*
	 * Update Notifications from own RSS Feed
	 */
	public function eventUpdateNotifications($observer)
	{
		Mage::getModel('downloadplus/feed')->checkUpdate();
	}

	/*
	 * Returns Session model
	 */
	protected function getSession()
	{
		return Mage::getSingleton('core/session');
	}

	protected function getValue(&$value, $default)
	{
		if (isset($value)) { return $value;	}
		return $default;
	}

	/*
	 * Adds Adminhtml Blocks
	 */
	public function eventAdminhtmlBlockHtmlBefore($observer)
	{
		$block = $observer->getEvent()->getBlock();
		$type = $block->getType();
		$id = $block->getId();

		switch ($id) {
			case 'customerViewAccordion':
				$block->getLayout()->createBlock('downloadplus/adminhtml_customer_edit_view_accordion')->updateAccordion($block);
				break;
		}
	}

	/*
	 * Adds Adminhtml Blocks
	 */
	public function eventCoreBlockAbstractToHtmlBefore($observer)
	{
	}

	public function eventAdminhtmlModelSaveBefore($observer)
	{
		$object = $observer->getObject();
		if ($object instanceof Mage_Downloadable_Model_Link) {
			// Remove line breaks possible from <textarea> fields
			$str = trim(preg_replace('/\r|\n|\r\n|\n\r/', ' ', $object->getTitle()));
			$object->setTitle($str);

			$str = trim(preg_replace('/\s+/', '', $object->getLinkUrl()));
			$object->setLinkUrl($str);
			
			$str = trim(preg_replace('/\s+/', '', $object->getSampleUrl()));
			$object->setSampleUrl($str);
		}
	}
	
	/*
	 * Saving a Downloadable Product
	 */
	public function eventAdminhtmlModelSaveAfter($observer)
	{
		$object = $observer->getObject();
		if ($object instanceof Mage_Downloadable_Model_Link) {
			// Overwrite title with data from use title select box
			$title = $object->getData('use_title');
			if ($object->getLinkId() && !empty($title)) {
				$object->setTitle($title);
				Mage::getModel('downloadplus/link_title')->getResource()->saveTitle($object);
			}
			
			// Set defaults from configuration for new link entries
			if ($object->getId()) {
				$config = Mage::getModel('downloadplus/config');
				$extension = Mage::getModel('downloadplus/link_extension');
				$extension->loadByLinkId($object->getId());
				if ($extension->getId()==null && $config->getCatalogProductDefaultExpiryType($object->getLinkType()) && $config->getCatalogProductDefaultExpiryDuration($object->getLinkType())) {
					$extension->setData('expiry', $config->getCatalogProductDefaultExpiryDuration($object->getLinkType()));
					$extension->setData('expire_on', $config->getCatalogProductDefaultExpiryType($object->getLinkType()));
				}
				// Save Link Attributes
				if ($object->getData('attributes')) {
					$extension->setAttributes($object->getData('attributes'));
				}
				$extension->save();
			}
		}
	}
	
	/*
	 * Deleting a Downloadable Product
	 */
	public function eventAdminhtmlModelDeleteAfter($observer)
	{
		$object = $observer->getObject();
		if ($object instanceof Mage_Downloadable_Model_Link) {
			if ($object->getId()) {
				$extension = Mage::getModel('downloadplus/link_extension');
				$extension->loadByLinkId($object->getId());
				if ($extension->getId()) {
					$extension->delete();
				}
			}
		}
	}
	
	/*
	 * Saving a Product in the Catalog (Admin)
	 */
	public function eventCatalogProductPrepareSave($observer)
	{
		$config = Mage::getModel('downloadplus/config');
		Mage::helper('downloadplus/download');
		Mage::getModel('downloadplus/download_detail');
		Mage::getModel('downloadplus/system_config_backend_catalog_product_filerelation');
		
		$product = $observer->getProduct();
		
		$downloadable = $observer->getRequest()->getPost('downloadable');
		$data = $observer->getRequest()->getPost('downloadplus');
		
		// Update file assocation for Links
		if ($links = $this->getValue($downloadable['link'], false)) {
			foreach ($links as $link) {
				Mage::dispatchEvent('downloadplus_catalog_product_update_downloadable_link', array('event' =>
					Mage::getModel('downloadplus/event')
						->setData('link', $link)
						->setData('product', $product)
				));
			}
		}

		// Update file assocation for Samples
		if ($samples = $this->getValue($downloadable['sample'], false)) {
			foreach ($samples as $sample) {
				Mage::dispatchEvent('downloadplus_catalog_product_update_downloadable_link', array('event' =>
					Mage::getModel('downloadplus/event')
						->setData('sample', $sample)
						->setData('product', $product)
				));
			}
		}

		// Add file details
		if ($files = $this->getValue($data['detail']['file'], false)) {
			foreach ($files as $key=>$file) {
				$detail = Mage::getModel('downloadplus/download_detail');
				$resource = $detail->convertTypeToResource($file);
				if ($id = $this->getValue($data['detail']['id'][$key], null)) {
					$detail->load($id);					
				} else {
					$detail->loadByFile($resource, $this->getStoreId($observer));
				}

				// Remove detail if in store and default is set
				if ($this->getStoreId($observer)==$detail->getStoreId() && isset($data['detail']['use_default'][$key]) && !is_null($data['detail']['use_default'][$key])) {
					$detail->delete();
				} else {
					if (!isset($data['detail']['use_default'][$key]) || (isset($data['detail']['use_default'][$key]) && is_null($data['detail']['use_default'][$key]))) {
						if ($detail->getData('store_id')!=$this->getStoreId($observer)) {
							// Set model as new, needs to be stored
							$detail->setId(null);
						}
						$detail->setData('store_id', $this->getStoreId($observer));
					} else {
						$detail->setData('store_id', 0);
					}

					// Only set data if model is for current store
					if ($detail->getData('store_id')==$this->getStoreId($observer)) {
								
						$detail->setData('product_id', $product->getId());
						$detail->setData('version', $this->getValue($data['detail']['fileversion'][$key], ''));
						$detail->setData('detail', $this->getValue($data['detail']['filedetail'][$key], ''));
	
						switch ($this->getValue($data['detail']['area'][$key], '')) {
							case 'active':
								if (!$detail->isActive()) { $detail->makeActive(); }
								break;
							case 'historical':
								if ($detail->isActive()) { $detail->makeHistorical(); }
								break;
						}
	
						if (!$detail->isActive()) {
							$relation = strtoupper($this->getValue($data['detail']['relation'][$key], ''));
							$_relation = substr($relation, 0, Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation::IS_CODELENGTH);
	
							if ($_relation==Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation::IS_LINK) {
								$detail->setData('link_id', ltrim($relation, Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation::IS_LINK));
								$detail->setData('link_sample_id', null);
								$detail->setData('sample_id', null);
							}
	
							if ($_relation==Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation::IS_LINKSAMPLE) {
								$detail->setData('link_id', null);
								$detail->setData('link_sample_id', ltrim($relation, Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation::IS_LINKSAMPLE));
								$detail->setData('sample_id', null);
							}
	
							if ($_relation==Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation::IS_SAMPLE) {
								$detail->setData('link_id', null);
								$detail->setData('link_sample_id', null);
								$detail->setData('sample_id', ltrim($relation, Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation::IS_SAMPLE));
							}
							$detail->setHidden($this->getValue($data['detail']['hidden'][$key], '0'));
							
							if ($_relation==Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation::IS_REMOVE_ASSOCIATION) {
								$detail->removeAssociation();
							}
						}
	
						$detail->save();
					}
				}
			}
		}

		// Add selected historical files
		if ($files = $this->getValue($data['add_historical_files'], false)) {
			foreach ($files as $file) {
				$detail = Mage::getModel('downloadplus/download_detail')->loadByFile($file);
				$detail->setData('product_id', $product->getId());
				$detail->makeHistorical();
				$detail->save();
			}
		}

		// Add additional Product downloads
		if ($links = $this->getValue($downloadable['productlink'], false)) {
			Mage::helper('downloadplus/download');
				
			foreach ($links as $link) {
				$download = Mage::getModel('downloadplus/link_product_item');
				if ($link['link_id']!=0) {
					$download->load($link['link_id']);
				}
				if ($link['is_delete']==1) {
					$download->delete();
				} else {
					$download->setStoreId(Mage::app()->getRequest()->getParam('store'));
					$download->setProductId($product->getId());
					$download->setLinkTitle(isset($link['title'])?$link['title']:null);
					$download->setUseDefaultTitle(isset($link['use_default_title'])?true:false);
					$download->setLinkType($link['type']);
					$download->setLinkUrl($link['link_url']);
					
					if ($download->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && !empty($link['amazon_s3_bucket']) && !empty($link['amazon_s3_file'])) {
						$download->setLinkUrl($link['amazon_s3_bucket'].'|'.$link['amazon_s3_file']);
						$download->setLinkFile('');
					}
					if ($download->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && isset($link['amazon_s3_object'])) {
						$download->setLinkUrl($link['amazon_s3_object']);
						$download->setLinkFile('');
					}
					if ($download->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF && isset($link['amazon_cf_object'])) {
						$download->setLinkUrl($link['amazon_cf_object']);
						$download->setLinkFile('');
					}
					if ($download->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL && isset($link['file_local_object'])) {
						$download->setLinkUrl('');
						$download->setLinkFile($link['file_local_object']);
					}
						
					$download->setSortOrder($link['sort_order']);

					$file = Zend_Json_Decoder::decode($link['file']);
					$file = $this->getValue($file[0], false);
					if ($file && $file['status']=='new') {
						$download->updateLinkFile($file['file'], $file['name'], $product);
					}
					$download->getDownloadDetail()->setFile($download->getLinkFile());
					$download->getDownloadDetail()->setDetail(isset($link['description'])?$link['description']:null);
					$download->getDownloadDetail()->setUseDefaultDetail(isset($link['use_default_description'])?true:false);

					// Save Link Attributes
					if (isset($link['attributes'])) {
						$download->setAttributes($link['attributes']);
					}
					
					$download->save();
				}
			}
		}

		// Deactivate Serialnumbers
		if ($value = $this->getValue($data['serialnumbers_deactivate'], false)) {
			$product->setData('downloadplus_serialnr_inactive', $value);
		}
		
		// Import Serialnumbers
		if ($serials = $this->getValue($data['serialnumbers'], false)) {
			$helper = Mage::helper('downloadplus');
			$serialPool = $this->getValue($data['serialnumberpool'], false);

			$helper->importSerialnumbers($serials, $serialPool, $product);
		}

		// Remove Serialnumbers
		if ($serials = $this->getValue($data['serialnumber_remove_id'], false)) {
			foreach ($serials as $serial) {
				$serialNumber = Mage::getModel('downloadplus/product_serialnumber');
				$serialNumber->load($serial);
				if ($serialNumber->getId()==$serial) {
					$serialNumber->delete();
				}
			}
		}

		// Update download additional settings
		if ($links = $this->getValue($data['settings']['link'], false)) {
			foreach ($links as $key=>$link) {
				$extension = Mage::getModel('downloadplus/link_extension');
				if ($link['link_id']>0) {
					$extension->loadByLinkId($link['link_id']);
					$extension->setData('link_id', $this->getValue($link['link_id'], null));
					$extension->setData('expiry', $this->getValue($link['expiry'], null));
					$extension->setData('expire_on', $this->getValue($link['expire_on'], $extension->EXPIRE_ON_NEVER));
					$extension->setData('serial_number_pool', $this->getValue($link['serial_number_pool'], null));
					$extension->setData('serial_number_pool_unlock', $this->getValue($link['serial_number_pool_unlock'], null));
						
					$extension->save();
				}
			}
		}

		// Clean cache
		Mage::app()->cleanCache('downloadplus_block_topdownloads');
		Mage::app()->cleanCache('downloadplus_block_updated');
	}

	/*
	 * Saving a Customer Account (Admin)
	 */
	public function eventCustomerPrepareSave($observer)
	{
		$config = Mage::getModel('downloadplus/config');
		$customer = $observer->getCustomer();
		$downloadable = $observer->getRequest()->getPost('downloadable');

		// Update file assocation for Links
		if ($links = $this->getValue($downloadable['link'], false)) {

			foreach ($links as $link) {
				$download = Mage::getModel('downloadplus/link_customer_item');
				if ($this->getValue($link['link_id'], 0)!=0) {
					$download->load($link['link_id']);
				}
				if ($this->getValue($link['is_delete'], 0)==1) {
					$download->delete();
				} else {
					$download->setOrderItemId($this->getValue($link['order_item_id'], null));
					$download->setLinkTitle($this->getValue($link['title'], ''));
					$download->setIsShareable($this->getValue($link['is_shareable'], 0));
					$download->setLinkType($this->getValue($link['type'], ''));
					if (isset($link['is_unlimited']) && $link['is_unlimited']==1) {
						$download->setNumberOfDownloadsBought(0);
					} else {
						$download->setNumberOfDownloadsBought($link['number_of_downloads']);
					}

					$download->setLinkUrl($this->getValue($link['link_url'], ''));

					$file = Zend_Json_Decoder::decode($this->getValue($link['file'], ''));
					$file = $this->getValue($file[0], false);
					if ($file && $file['status']=='new') {
						$download->updateLinkFile($file['file'], $file['name'], $customer);
					}
					$download->getDownloadDetail()->setFile($download->getLinkFile());
					$download->getDownloadDetail()->setDetail($this->getValue($link['description'], ''));

					$download->save();

					if ($this->getValue($link['notify_customer'], 0)==1) {
				    	$messages = Mage::getSingleton('core/session')->getMessages();
						if ($download->notifyCustomer()) {
				    		$message = $messages->getMessageByIdentifier('downloadplus-notify-customer-email-success');
				    		if (!$message) {
				    			$message = Mage::getModel('core/message_notice', Mage::helper('downloadplus')->__('Notification to Customer successfully sent.'));
				    			$message->setIdentifier('downloadplus-notify-customer-email-success');
				    			$messages->add($message);
				    		}
						} else {
				    		$message = $messages->getMessageByIdentifier('downloadplus-notify-customer-email-failed');
				    		if (!$message) {
				    			$message = Mage::getModel('core/message_notice', Mage::helper('downloadplus')->__('Notification to Customer failed to send.'));
				    			$message->setIdentifier('downloadplus-notify-customer-email-failed');
				    			$messages->add($message);
				    		}
						}
					}
				}
			}
		}

		// Update serial numbers for Links
		if ($serials = $this->getValue($downloadable['serial'], false)) {

			foreach ($serials as $serial) {
				$download = Mage::getModel('downloadplus/link_purchased_item_serialnumber');
				if ($this->getValue($serial['serial_id'], 0)!=0) {
					$download->load($serial['serial_id']);
				}
				if ($this->getValue($serial['is_delete'], 0)==1) {
					$download->delete();
				} else {
					$download->setOrderItemId($this->getValue($serial['order_item_id'], null));
					$download->setSerialTitle($this->getValue($serial['title'], ''));
					$download->setSerialNumber($this->getValue($serial['number'], ''));
					$download->save();

					if ($this->getValue($serial['notify_customer'], 0)==1) {
				    	$messages = Mage::getSingleton('core/session')->getMessages();
						if ($download->notifyCustomer()) {
				    		$message = $messages->getMessageByIdentifier('downloadplus-notify-customer-email-success');
				    		if (!$message) {
				    			$message = Mage::getModel('core/message_notice', Mage::helper('downloadplus')->__('Notification to Customer successfully sent.'));
				    			$message->setIdentifier('downloadplus-notify-customer-email-success');
				    			$messages->add($message);
				    		}
						} else {
				    		$message = $messages->getMessageByIdentifier('downloadplus-notify-customer-email-failed');
				    		if (!$message) {
				    			$message = Mage::getModel('core/message_notice', Mage::helper('downloadplus')->__('Notification to Customer failed to send.'));
				    			$message->setIdentifier('downloadplus-notify-customer-email-failed');
				    			$messages->add($message);
				    		}
						}
					}
				}
			}
		}

		// Update expiration data on downloads
		if ($expirations = $this->getValue($downloadable['expires_on'], false)) {
			foreach ($expirations as $key=>$expiration) {
				$extension = Mage::getModel('downloadplus/link_purchased_item_extension')->loadByItemId($key);
				if ($extension->getId()) {
					$extension->setExpiresOn($expiration);
					$extension->save();
				}
			}
		}
		
		// Update status on downloads
		if ($statuses = $this->getValue($downloadable['status'], false)) {
			foreach ($statuses as $key=>$status) {
				if ($link = Mage::getModel('downloadplus/link_purchased_item')->load($key)) {
					if (($link->getStatus()==Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE
							|| $link->getStatus()==Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_PENDING
							|| $link->getStatus()==Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT)
						 && $status==Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
						$link->setStatus($status);
						$link->save();
					}
					if ((($link->getStatus()==Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED
							|| $link->getStatus()==Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_PENDING
							|| $link->getStatus()==Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT)
						&& $status==Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE)
							|| ($status==Pisc_Downloadplus_Model_Config::LINK_STATUS_REFRESH)
							|| ($status==Pisc_Downloadplus_Model_Config::LINK_STATUS_UPDATE)) {
						$link->setNumberOfDownloadsUsed(0);
						$link->setStatus(Pisc_Downloadplus_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE);
						$link->save();
						Mage::getModel('downloadplus/link_purchased_item_extension')->refreshExpiration($link);
					}
					if ($status==Pisc_Downloadplus_Model_Config::LINK_STATUS_UPDATE) {
						if ($origLink = $link->getLink()) {
							$link->setLinkFile($origLink->getLinkFile());
							$link->setLinkUrl($origLink->getLinkUrl());
							$link->setLinkType($origLink->getLinkType());
							$link->save();
						}
					}
				}
			}
		}

	}

	/*
	 * Updates file history for links
	 */
	public function eventDownloadableProductUpdateLinkHistory($observer)
	{
		$link = $observer->getEvent()->getLink();
		$sample = $observer->getEvent()->getSample();
		$product = $observer->getEvent()->getProduct();
		
		/* Link File History */
		if (isset($link['file'])) {
			$file = Zend_Json_Decoder::decode($link['file']);
			$file = $this->getValue($file[0], false);

			if ($this->getValue($link['type'], null)==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE && $file) {
				$detail = Mage::getModel('downloadplus/download_detail');
				
				if ($this->getValue($link['link_id'], 0)!=0) {
					$detail->loadByLinkId($link['link_id']);
				}
				if (!$detail->getId()) {
					$detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK.$file['file']);
				}
				// Update file association if related file is different
				if ($detail->getId() && $this->getValue($file['status'],'')=='new') {
					// We need to update the assocation in case of a new entry
					$detail->setData('link_id', $this->getValue($link['link_id'], 0));
					// Make file recorded in database historical
					$detail->makeHistorical();
					$detail->save();
					// Create new association for this file
					$detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK.$file['file']);
				}
				$detail->setData('product_id', $product->getId());
				$detail->setData('link_id', $this->getValue($link['link_id'], 0));
				$detail->setData('link_sample_id', null);
				$detail->setData('sample_id', null);
				if ($link['is_delete']==1) {
					// Make File historical if being deleted
					$detail->makeHistorical();
				} else {
					// Update Active Status
					$detail->makeActive();
				}
				$detail->save();
			}
		}

		/* Link Sample File History */
		if (isset($link['sample']['file'])) {
			$file = Zend_Json_Decoder::decode($link['sample']['file']);
			$file = $this->getValue($file[0], false);

			if ($this->getValue($link['sample']['type'], null)==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE && $file) {
				$detail = Mage::getModel('downloadplus/download_detail');
				
				if ($this->getValue($link['link_id'], 0)!=0) {
					$detail->loadByLinkSampleId($link['link_id']);
				}
				if (!$detail->getId()) {
					$detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK_SAMPLE.$file['file']);
				}
				// Update file association if related file is different
				if ($detail->getId() && $this->getValue($file['status'],'')=='new') {
					// We need to update the assocation in case of a new entry
					$detail->setData('link_sample_id', $this->getValue($link['link_id'], 0));
					// Make file recorded in database historical
					$detail->makeHistorical();
					$detail->save();
					// Create new association for this file
					$detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK_SAMPLE.$file['file']);
				}
				$detail->setData('product_id', $product->getId());
				$detail->setData('link_id', null);
				$detail->setData('link_sample_id', $this->getValue($link['link_id'], 0));
				$detail->setData('sample_id', null);
				if ($this->getValue($link['is_delete'], null)==1) {
					// Make File historical if being deleted
					$detail->makeHistorical();
				} else {
					// Update Active Status
					$detail->makeActive();
				}
				$detail->save();
			}
		}

		/* Sample File History */
		if (isset($sample['file'])) {
			$file = Zend_Json_Decoder::decode($sample['file']);
			$file = $this->getValue($file[0], false);

			if ($this->getValue($sample['type'], null)==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE && $file) {
					
				$detail = Mage::getModel('downloadplus/download_detail');
				
				if ($this->getValue($sample['sample_id'], 0)!=0) {
					$detail->loadBySampleId($sample['sample_id']);
				}
				if (!$detail->getId()) {
					$detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_SAMPLE.$file['file']);
				}
				// Update file association if related file is different
				if ($detail->getId() && $this->getValue($file['status'],'')=='new') {
					// We need to update the assocation in case of a new entry
					$detail->setData('sample_id', $this->getValue($sample['sample_id'], 0));
					// Make file recorded in database historical
					$detail->makeHistorical();
					$detail->save();
					// Create new association for this file
					$detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_SAMPLE.$file['file']);
				}
				$detail->setData('product_id', $product->getId());
				$detail->setData('link_id', null);
				$detail->setData('link_sample_id', null);
				$detail->setData('sample_id', $this->getValue($sample['sample_id'], 0));
				if ($this->getValue($sample['is_delete'], null)==1) {
					// Make File historical if being deleted
					$detail->makeHistorical();
				} else {
					// Update Active Status
					$detail->makeActive();
				}
				$detail->save();
			}
		}
		
	}
	
	/*
	 * Duplicating a Catalog Product
	 */
	public function eventCatalogProductDuplicate($observer)
	{
		$product = $observer->getProduct();
		$config = Mage::getModel('downloadplus/config');
		
		Mage::getModel('downloadable/product_type');
		
		if ($product->getTypeId()==Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE && $product->getIsDuplicate() && $product->getOriginalId() && $product->getId()) {
			// Duplicate Links
			if ($config->isCatalogProductDuplicateLinks()) {
				$items = Mage::getModel('downloadable/link')->getCollection()
								->addProductToFilter($product->getOriginalId())
								->addTitleToResult($product->getStoreId())
								->addPriceToResult($product->getStore()->getWebsiteId());
				
				foreach ($items as $item) {
					$originalId = $item->getId();
					$item->setId(null);
					$item->setProductId($product->getId());
					$item->setData('store_id', Mage::app()->getStore()->getId());
					$item->setData('website_id', Mage::app()->getWebsite()->getId());
					$item->save();
					// Duplicate Link Extension
					$originalExtension = Mage::getModel('downloadplus/link_extension')->load($originalId, 'link_id');
					$extension = Mage::getModel('downloadplus/link_extension')->load($item->getId(), 'link_id');
					$extension->setData($originalExtension->getData());
					$extension->setId($extension->getOrigData('id'));
					$extension->setLinkId($item->getId());
					$extension->save();
				}
			}
			
			// Duplicate Samples
			if ($config->isCatalogProductDuplicateSamples()) {
				$items = Mage::getModel('downloadable/sample')->getCollection()
								->addProductToFilter($product->getOriginalId())
								->addTitleToResult($product->getStoreId());
				
				foreach ($items as $item) {
					$item->setId(null);
					$item->setProductId($product->getId());
					$item->setData('store_id', Mage::app()->getStore()->getId());
					$item->setData('website_id', Mage::app()->getWebsite()->getId());
					$item->save();
				}
			}
		}
	}

}
