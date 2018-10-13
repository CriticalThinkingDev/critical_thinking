<?php
/**
 * Downloadable product type model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.11
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

	public function getSamples($product = null)
	{
		$product = $this->getProduct($product);
		/* @var Mage_Catalog_Model_Product $product */
		if (is_null($product->getDownloadableSamples())) {
			$_sampleCollection = Mage::getModel('downloadplus/sample')->getCollection()
				->addProductToFilter($product->getId())
				->addTitleToResult($product->getStoreId());
			$product->setDownloadableSamples($_sampleCollection);
		}

		return $product->getDownloadableSamples();
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
    					    $detail = Mage::getModel('downloadplus/download_detail')->loadBySampleId($sampleItem['sample_id']);
    					    if ($detail->getId()) {
    					        $detail->makeHistorical();
    					        $detail->save();
    					    }
    					}
    				} else {
    					unset($sampleItem['is_delete']);
    					if (!$sampleItem['sample_id']) {
    						unset($sampleItem['sample_id']);
    					}
    					$sampleModel = Mage::getModel('downloadplus/sample');
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

    					if ($sampleModel->getSampleType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD && !empty($sampleItem['editionguard_object'])) {
    						$sampleModel->setSampleUrl($sampleItem['editionguard_object']);
    						$sampleModel->setSampleFile('');
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

    					if (isset($sampleItem['image']['is_delete']) && $sampleItem['image']['is_delete']=='1') {
    						$sampleModel = Mage::getModel('downloadplus/sample_image')->setSample($sampleModel)->remove()->getSample();
    					} else {
    						if (isset($sampleItem['image']['file'])) {
    							$image = Zend_Json::decode($sampleItem['image']['file']);
    							$sampleModel->updateImageFileFromUpload($image[0]['file'], $image[0]['name']);
    						}
    					}

    					$sampleModel->unsetData('amazon_s3_object');
    					$sampleModel->unsetData('amazon_s3_bucket');
    					$sampleModel->unsetData('amazon_s3_file');
    					$sampleModel->unsetData('amazon_cf_object');
    					$sampleModel->unsetData('file_local_object');
    					$sampleModel->unsetData('editionguard_object');

    					$sampleModel->save();

    					// Update Download Detail with this Data
    					if ($sampleModel->getSampleFile()) {
        					$detail = Mage::getModel('downloadplus/download_detail')->loadBySampleId($sampleModel->getId());
        					$detail->setProductId($sampleModel->getProductId());
        					$detail->setStoreId($sampleModel->getStoreId());
        					$detail->setFile($sampleModel->getSampleFile());
        					$detail->makeActive();
        					$detail->save();

       					    Mage::app()->cleanCache(Array('downloadplus_downloads'));
    					}
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
    						$detail = Mage::getModel('downloadplus/download_detail')->loadByLinkId($linkItem['link_id']);
    						if ($detail->getId()) {
    						    $detail->makeHistorical();
    						    $detail->save();
    						}
    						$detail = Mage::getModel('downloadplus/download_detail')->loadByLinkSampleId($linkItem['link_id']);
    						if ($detail->getId()) {
    						    $detail->makeHistorical();
    						    $detail->save();
    						}
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
    					$linkModel = Mage::getModel('downloadplus/link')
				    					->setData($linkItem)
				    					->setPreparedForSave(true)
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

    						if ($sample['type']==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD && !empty($sample['editionguard_object'])) {
    							$linkModel->setSampleUrl($sample['editionguard_object']);
    							$linkModel->setSampleFile('');
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

    					if ($linkModel->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD && !empty($linkItem['editionguard_object'])) {
    						$linkModel->setLinkUrl($linkItem['editionguard_object']);
    						$linkModel->setLinkFile('');
    					}

    					if ($linkModel->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_BUILDER && !empty($linkItem['builder_object'])) {
    						$linkModel->setLinkUrl($linkItem['builder_object']);
    						$linkModel->setLinkFile('');
    					}

    					if ($linkModel->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_MAGAZINE && !empty($linkItem['magazine_object'])) {
    						$linkModel->setLinkUrl($linkItem['magazine_object']);
    						$linkModel->setLinkFile('');
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

    					if (isset($linkItem['image']['is_delete']) && $linkItem['image']['is_delete']=='1') {
    						$linkModel = Mage::getModel('downloadplus/link_image')->setLink($linkModel)->remove()->getLink();
    					} else {
							if (isset($linkItem['image']['file'])) {
								$image = Zend_Json::decode($linkItem['image']['file']);
								if (isset($image[0])) {
									$linkModel->updateImageFileFromUpload($image[0]['file'], $image[0]['name']);
								}
							}
    					}

    					$linkModelDataChanged = $linkModel->hasDataChanges();

    					// Prepare Link Model Save
    					$linkModel->unsetData('amazon_s3_object');
    					$linkModel->unsetData('amazon_s3_bucket');
    					$linkModel->unsetData('amazon_s3_file');
    					$linkModel->unsetData('amazon_cf_object');
    					$linkModel->unsetData('file_local_object');
    					$linkModel->unsetData('editionguard_object');
    					$linkModel->unsetData('builder_object');

    					$linkModel->save();

    					// Update Download Detail with this Data
    					if ($linkModel->getLinkFile()) {
    					    //$detail = Mage::getModel('downloadplus/download_detail')->loadByLinkId($linkModel->getId());
    					    $detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK.$linkModel->getLinkFile());
    					    $detail->setLinkId($linkModel->getId());
    					    $detail->setProductId($linkModel->getProductId());
    					    $detail->setStoreId($linkModel->getStoreId());
    					    $detail->setFile($linkModel->getLinkFile());
    					    $detail->makeActive();
    					    $detail->save();
    					}

    					if ($linkModel->getSampleFile()) {
    					    //$detail = Mage::getModel('downloadplus/download_detail')->loadByLinkSampleId($linkModel->getId());
    					    $detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK_SAMPLE.$linkModel->getSampleFile());
    					    $detail->setLinkSampleId($linkModel->getId());
    					    $detail->setProductId($linkModel->getProductId());
    					    $detail->setStoreId($linkModel->getStoreId());
    					    $detail->setFile($linkModel->getSampleFile());
    					    $detail->makeActive();
    					    $detail->save();
    					}

    					if ($linkModel->getLinkFile() || $linkModel->getSampleFile()) {
    					    Mage::app()->cleanCache(Array('downloadplus_downloads'));
    					}

    					// Trigger Link Replacing
    					if (isset($linkItem['replaces_id'])) {
    						Mage::dispatchEvent('downloadplus_event_queue_add', Array(
    							'code' => 'downloadable-link-replace',
    							'related_id' => $linkModel->getId(),
    							'attributes' => Array('link_id'=>$linkItem['replaces_id'])
    						));
    					}

    					// Add this to Link History
    					Mage::dispatchEvent('downloadplus_link_history_add', Array('link'=>$linkModel, 'link_data_changed'=>$linkModelDataChanged));
    				}
    			}
    			if ($_deleteItems) {
    				Mage::getResourceModel('downloadplus/link')->deleteItems($_deleteItems);
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
