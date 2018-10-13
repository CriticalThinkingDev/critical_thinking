<?php
/**
 * Downloadplus Resource Helper
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2012 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.1
 */

class Pisc_Downloadplus_Helper_Resource extends Mage_Core_Helper_Abstract
{
	
	public function getResource($link, $sample=false)
	{
		$resource = null;
		
		if ($link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_URL
			|| $link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3
			|| $link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF
			|| $link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD) {
				if ($sample) {
					$resource = $link->getSampleUrl();	
				} else {
					$resource = $link->getLinkUrl();
				}
				
		} elseif ($link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_BUILDER) {
			if ($sample) {
				$resource = Mage::helper('downloadable/file')->getFilePath(
					Pisc_Downloadplus_Model_Link::getBasePath($link->getSampleType()), $link->getSampleFile()
				);
			} else {					
				$resource = Mage::helper('downloadable/file')->getFilePath(
					Pisc_Downloadplus_Model_Link::getBasePath($link->getLinkType()), $link->getLinkFile()
				);
			}
			
		} elseif ($link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE
					|| $link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
			if ($link instanceof Pisc_Downloadplus_Model_Link_Product_Item) {
				$resource = Mage::helper('downloadable/file')->getFilePath(
					Pisc_Downloadplus_Model_Product_Download::getBasePath(), $link->getLinkFile()
				);
			} elseif ($link instanceof Pisc_DownloadplusBonus_Model_Link_Bonus_Item || $link instanceof Pisc_DownloadplusBonus_Model_Link_Purchased_Bonus_Item) {
				$resource = Mage::helper('downloadable/file')->getFilePath(
						Pisc_DownloadplusBonus_Model_Product_Bonus::getBasePath(), $link->getLinkFile()
				);
			} elseif ($link instanceof Pisc_Downloadplus_Model_Link_Customer_Item) {
				$resource = Mage::helper('downloadable/file')->getFilePath(
					Pisc_Downloadplus_Model_Customer_Download::getBasePath(), $link->getLinkFile()
				);
			} else {
				if ($sample) {
					$resource = Mage::helper('downloadable/file')->getFilePath(
						Pisc_Downloadplus_Model_Link::getBasePath($link->getSampleType()), $link->getSampleFile()
					);
				} else {
					$resource = Mage::helper('downloadable/file')->getFilePath(
						Pisc_Downloadplus_Model_Link::getBasePath($link->getLinkType()), $link->getLinkFile()
					);
				}
			}
		}

		return $resource;
	}
	
	public function getResourceType($link, $sample=false)
	{
		$resourceType = null;
		
		if ($link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_URL
			|| $link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3
			|| $link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF
			|| $link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD) {
				if ($sample) {
					$resourceType = $link->getSampleType();
				} else {
					$resourceType = $link->getLinkType();
				}
					
		} elseif ($link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_BUILDER) {
			if ($sample) {
				$resourceType = $link->getSampleType();
			} else {
				$resourceType = $link->getLinkType();
			}
			
		} elseif ($link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE
					|| $link->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
			$resourceType = Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE;
		}
		
		return $resourceType;
	}

	public function getSampleResource($sample)
	{
		$resource = null;
		
		if ($sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_URL
			|| $sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3
			|| $sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF
			|| $sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD) {
				$resource = $sample->getSampleUrl();
				
		} elseif ($sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE
				|| $sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
			$resource = Mage::helper('downloadable/file')->getFilePath(
				Pisc_Downloadplus_Model_Sample::getBasePath($sample->getSampleType()), $sample->getSampleFile()
			);
		}
		
		return $resource;
	}

	public function getSampleResourceType($sample)
	{
		$resourceType = null;
		
		if ($sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_URL
			|| $sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3
			|| $sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF
			|| $sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD) {
			$resourceType = $sample->getSampleType();
			
		} elseif ($sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE
					|| $sample->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
			$resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
		}
		
		return $resourceType;
	}
	
}
