<?php
/**
 * Downloadable product type model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.1
 *
 */

class Pisc_Downloadplus_Model_Product_Type extends Mage_Downloadable_Model_Product_Type
{

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

    					if ($sampleModel->getSampleType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3) {
    						$sampleModel->setSampleUrl($sampleItem['amazon_s3_object']);
    					}
    					if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
    						$sampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
    								Mage_Downloadable_Model_Sample::getBaseTmpPath(),
    								Mage_Downloadable_Model_Sample::getBasePath(),
    								$files
    						);
    						$sampleModel->setSampleFile($sampleFileName);
    					}
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
    						if ($sample['type']==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && isset($sample['amazon_s3_object'])) {
    							$linkModel->setSampleUrl($sample['amazon_s3_object']);
    						}
    						$linkModel->setSampleType($sample['type']);
    						$sampleFile = Zend_Json::decode($sample['file']);
    					}
    					if ($linkModel->getLinkType() == Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 && isset($linkItem['amazon_s3_object'])) {
    						$linkModel->setLinkUrl($linkItem['amazon_s3_object']);
    					}
    					if ($linkModel->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
    						$linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
    								Mage_Downloadable_Model_Link::getBaseTmpPath(),
    								Mage_Downloadable_Model_Link::getBasePath(),
    								$files
    						);
    						$linkModel->setLinkFile($linkFileName);
    					}
    					if ($linkModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
    						$linkSampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
    								Mage_Downloadable_Model_Link::getBaseSampleTmpPath(),
    								Mage_Downloadable_Model_Link::getBaseSamplePath(),
    								$sampleFile
    						);
    						$linkModel->setSampleFile($linkSampleFileName);
    					}
    					$linkModel->unsetData('amazon_s3_object');
    					$linkModel->save();
    				}
    			}
    			if ($_deleteItems) {
    				Mage::getResourceModel('downloadable/link')->deleteItems($_deleteItems);
    			}
    		}
    	}
    	
    	return $this;
    	 
        parent::save($product);

        $product = $this->getProduct($product);

        if ($data = $product->getDownloadableData()) {
        	
        	Mage::helper('downloadplus/download');
        	
            if (isset($data['link'])) {
                foreach ($data['link'] as $linkItem) {
                    if ($linkItem['is_delete'] != '1') {
                        unset($linkItem['is_delete']);
                        
                        $linkModel = Mage::getModel('downloadable/link')
                            ->setData($linkItem)
                            ->setLinkType($linkItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId())
                            ->setWebsiteId($product->getStore()->getWebsiteId());

                        if ($sample && isset($sample['type'])) {
                            if ($sample['type']==Pisc_Downloadable_Helper_Download::LINK_TYPE_AWSS3 && $sample['amazon_s3_object']!='') {
                                $linkModel->setSampleUrl($sample['amazon_s3_object']);
                            }
                            $linkModel->setSampleType($sample['type']);
                        }
                        $linkModel->save();
                    }
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
