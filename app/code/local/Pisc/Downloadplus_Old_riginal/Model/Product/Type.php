<?php
/**
 * Downloadable product type model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.5
 *
 */

class Pisc_Downloadplus_Model_Product_Type extends Mage_Downloadable_Model_Product_Type
{

	/*
	 * Returns the associated Downloadable Links
	 */
	public function getLinks($product = null)
	{
		$product = $this->getProduct($product);
		/* @var Mage_Catalog_Model_Product $product */
		if (is_null($product->getDownloadableLinks())) {
			$_linkCollection = Mage::getModel('downloadplus/link')->getCollection()
				->addProductToFilter($product->getId())
				->addTitleToResult($product->getStoreId())
				->addPriceToResult($product->getStore()->getWebsiteId());
			$linksCollectionById = array();
			foreach ($_linkCollection as $link) {
				/* @var Mage_Downloadable_Model_Link $link */
				$link->setProduct($product);
				$linksCollectionById[$link->getId()] = $link;
			}
			$product->setDownloadableLinks($linksCollectionById);
		}
		return $product->getDownloadableLinks();
	}
	
	/*
	 * Save data related to DownloadPlus AWS extension
	 */
    public function save($product = null)
    {
    	$product = $this->getProduct($product);
    	/* @var Mage_Catalog_Model_Product $product */
    	
    	if ($data = $product->getDownloadableData()) {
    		if (isset($data['sample'])) {
    			$_deleteItems = array();
    			foreach ($data['sample'] as $sampleItem) {
    				if ($sampleItem['is_delete'] == '1') {
    					if ($sampleItem['sample_id']) {
    						$_deleteItems[] = $sampleItem['sample_id'];
    					}
    				} else {
    					unset($sampleItem['is_delete']);
    					if (!$sampleItem['sample_id']) {
    						unset($sampleItem['sample_id']);
    					}
    					$sampleModel = Mage::getModel('downloadable/sample');
    					$files = array();
    					if (isset($sampleItem['file'])) {
    						$files = Zend_Json::decode($sampleItem['file']);
    						unset($sampleItem['file']);
    					}
    	
    					$sampleModel->setData($sampleItem)
    								->setSampleType($sampleItem['type'])
    								->setProductId($product->getId())
    								->setStoreId($product->getStoreId());

    					if ($sampleModel->getSampleType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && !empty($sampleItem['amazon_s3_bucket']) && !empty($sampleItem['amazon_s3_file'])) {
    						$sampleModel->setSampleUrl($sampleItem['amazon_s3_bucket'].'|'.$sampleItem['amazon_s3_file']);
    						$sampleModel->setSampleFile('');
    					}
    					if ($sampleModel->getSampleType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && !empty($sampleItem['amazon_s3_object'])) {
   							$sampleModel->setSampleUrl($sampleItem['amazon_s3_object']);
    						$sampleModel->setSampleFile('');
    					}

    					if ($sampleModel->getSampleType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF && !empty($sampleItem['amazon_cf_object'])) {
    						$sampleModel->setSampleUrl($sampleItem['amazon_cf_object']);
    						$sampleModel->setSampleFile('');
    					}

    					if ($sampleModel->getSampleType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL && !empty($sampleItem['file_local_object'])) {
    						$sampleModel->setSampleUrl('');
    						$sampleModel->setSampleFile($sampleItem['file_local_object']);
    					}
    						
    					if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
    						$sampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
    								Mage_Downloadable_Model_Sample::getBaseTmpPath(),
    								Mage_Downloadable_Model_Sample::getBasePath(),
    								$files
    						);
    						$sampleModel->setSampleUrl('');
    						$sampleModel->setSampleFile($sampleFileName);
    					}
    					
    					$sampleModel->unsetData('amazon_s3_object');
    					$sampleModel->unsetData('amazon_s3_bucket');
    					$sampleModel->unsetData('amazon_s3_file');
    					$sampleModel->unsetData('amazon_cf_object');
    					$sampleModel->unsetData('file_local_object');
    						
    					$sampleModel->save();
    				}
    			}
    			if ($_deleteItems) {
    				Mage::getResourceModel('downloadable/sample')->deleteItems($_deleteItems);
    			}
    		}
    		if (isset($data['link'])) {
    			$_deleteItems = array();
    			foreach ($data['link'] as $linkItem) {
    				if ($linkItem['is_delete'] == '1') {
    					if ($linkItem['link_id']) {
    						$_deleteItems[] = $linkItem['link_id'];
    					}
    				} else {
    					
    					Mage::helper('downloadplus/download');
    					
    					unset($linkItem['is_delete']);
    					if (!$linkItem['link_id']) {
    						unset($linkItem['link_id']);
    					}
    					$files = array();
    					if (isset($linkItem['file'])) {
    						$files = Zend_Json::decode($linkItem['file']);
    						unset($linkItem['file']);
    					}
    					$sample = array();
    					if (isset($linkItem['sample'])) {
    						$sample = $linkItem['sample'];
    						unset($linkItem['sample']);
    					}
    					$linkModel = Mage::getModel('downloadable/link')
				    					->setData($linkItem)
				    					->setLinkType($linkItem['type'])
				    					->setProductId($product->getId())
				    					->setStoreId($product->getStoreId())
				    					->setWebsiteId($product->getStore()->getWebsiteId());
    					if (null === $linkModel->getPrice()) {
    						$linkModel->setPrice(0);
    					}
    					if ($linkModel->getIsUnlimited()) {
    						$linkModel->setNumberOfDownloads(0);
    					}
    					$sampleFile = array();
    					if ($sample && isset($sample['type'])) {
    						if ($sample['type'] == 'url' && $sample['url'] != '') {
    							$linkModel->setSampleUrl($sample['url']);
    						}
    						if ($sample['type']==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && !empty($sample['amazon_s3_bucket']) && !empty($sample['amazon_s3_file'])) {
    							$linkModel->setSampleUrl($sample['amazon_s3_bucket'].'|'.$sample['amazon_s3_file']);
    							$linkModel->setSampleFile('');
    						}
    						if ($sample['type']==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && !empty($sample['amazon_s3_object'])) {
    							$linkModel->setSampleUrl($sample['amazon_s3_object']);
    							$linkModel->setSampleFile('');
    						}
    						
    						if ($sample['type']==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF && !empty($sample['amazon_cf_object'])) {
    							$linkModel->setSampleUrl($sample['amazon_cf_object']);
    							$linkModel->setSampleFile('');
    						}
    						
    						if ($sample['type']==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL && !empty($sample['file_local_object'])) {
    							$linkModel->setSampleUrl('');
    							$linkModel->setSampleFile($sample['file_local_object']);
    						}

    						if ($sample['type']==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE) {
    							$sampleFile = Zend_Json::decode($sample['file']);
    						}
    						$linkModel->setSampleType($sample['type']);
    					}
    					
    					if ($linkModel->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && !empty($linkItem['amazon_s3_bucket']) && !empty($linkItem['amazon_s3_file'])) {
    						$linkModel->setLinkUrl($linkItem['amazon_s3_bucket'].'|'.$linkItem['amazon_s3_file']);
    						$linkModel->setLinkFile('');
    					}
    					if ($linkModel->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && !empty($linkItem['amazon_s3_object'])) {
    						$linkModel->setLinkUrl($linkItem['amazon_s3_object']);
    						$linkModel->setLinkFile('');
    					}
    					
    					if ($linkModel->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF && !empty($linkItem['amazon_cf_object'])) {
    						$linkModel->setLinkUrl($linkItem['amazon_cf_object']);
    						$linkModel->setLinkFile('');
    					}

    					if ($linkModel->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL && !empty($linkItem['file_local_object'])) {
    						$linkModel->setLinkUrl('');
    						$linkModel->setLinkFile($linkItem['file_local_object']);
    					}
    						
    					if ($linkModel->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
    						$linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
    								Mage_Downloadable_Model_Link::getBaseTmpPath(),
    								Mage_Downloadable_Model_Link::getBasePath(),
    								$files
    						);
    						$linkModel->setLinkUrl('');
    						$linkModel->setLinkFile($linkFileName);
    					}
    					if ($linkModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
    						$linkSampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
    								Mage_Downloadable_Model_Link::getBaseSampleTmpPath(),
    								Mage_Downloadable_Model_Link::getBaseSamplePath(),
    								$sampleFile
    						);
    						$linkModel->setSampleUrl('');
    						$linkModel->setSampleFile($linkSampleFileName);
    					}
    					
    					$linkModel->unsetData('amazon_s3_object');
    					$linkModel->unsetData('amazon_s3_bucket');
    					$linkModel->unsetData('amazon_s3_file');
    					$linkModel->unsetData('amazon_cf_object');
    					$linkModel->unsetData('file_local_object');
    					
    					$linkModel->save();
    				}
    			}
    			if ($_deleteItems) {
    				Mage::getResourceModel('downloadable/link')->deleteItems($_deleteItems);
    			}
    		}
    	}
    	
    	return $this;
    }
    
    public function beforeSave($product = null)
    {
    	parent::beforeSave($product);
    	
    	/* Correct Mistake in Data Field Name in Magento Core */
    	if (!is_null($this->getProduct($product)->getTypeHasRequiredOptions())) {
    		$this->getProduct($product)->setRequiredOptions($this->getProduct($product)->getTypeHasRequiredOptions());
    	}
    	if (!is_null($this->getProduct($product)->getTypeHasOptions())) {
    		$this->getProduct($product)->setHasOptions($this->getProduct($product)->getTypeHasOptions());
    	}
    }
    
}
