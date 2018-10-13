<?php
/**
 * DownloadPlus Magento Helper
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.1
 */

class Pisc_Downloadplus_Helper_Magento extends Mage_Core_Helper_Abstract
{

	public function isEnterpriseEdition()
	{
		return Mage::getConfig ()->getModuleConfig ('Enterprise_Enterprise')
				&& Mage::getConfig ()->getModuleConfig ('Enterprise_AdminGws')
				&& Mage::getConfig ()->getModuleConfig ('Enterprise_Checkout')
				&& Mage::getConfig ()->getModuleConfig ('Enterprise_Customer');
	}

	public function isProfessionalEdition()
	{
		return Mage::getConfig ()->getModuleConfig ('Enterprise_Enterprise')
				&& !Mage::getConfig ()->getModuleConfig ('Enterprise_AdminGws')
				&& !Mage::getConfig ()->getModuleConfig ('Enterprise_Checkout')
				&& !Mage::getConfig ()->getModuleConfig ('Enterprise_Customer');
	}

	public function isCommunityEdition()
	{
		return !$this->isEnterpriseEdition() && !$this->isProfessionalEdition();
	}

	protected function convertVersion($version)
	{
		if ($this->isEnterpriseEdition()) {
			if (version_compare($version, '1.14.0', '>=' )) { $version = '1.9.0'; }
			elseif (version_compare($version, '1.13.0', '>=' )) { $version = '1.8.0'; }
			elseif (version_compare($version, '1.12.0', '>=' )) { $version = '1.7.0'; }
			elseif (version_compare($version, '1.11.0', '>=' )) { $version = '1.6.0'; }
			elseif (version_compare($version, '1.9.1', '>=' )) { $version = '1.5.0'; }
			elseif (version_compare($version, '1.9.0', '>=' )) { $version =  '1.4.2'; }
			elseif (version_compare($version, '1.8.0', '>=' )) { $version = '1.3.1'; }
		}
		
		if ($this->isProfessionalEdition()) {
			if (version_compare($version, '1.8.0', '>=' )) { $version = '1.4.1'; }
			elseif (version_compare($version, '1.7.0', '>=' )) { $version =  '1.3.1'; }
		}
		
		return $version;		
	}
	
	public function isVersion($reqVersion, $compare='>=')
	{
		$version = $this->convertVersion(Mage::getVersion());
		return version_compare($version, $reqVersion, $compare);
	}

	public function isMajorVersion($reqVersion, $compare='>=')
	{
		$version = $this->getMajorVersion();
		if ($version) {
			return version_compare($version, $reqVersion, $compare);
		}
		return false;
	}

	public function getMajorVersion()
	{
		$majVersion = null;
		$version = explode('.', $this->convertVersion(Mage::getVersion()));
		if (isset($version[0]) && isset($version[1])) {
			$majVersion = $version[0].'.'.$version[1];
		}
		return $majVersion;
	}
	
	public function isCompatible()
	{
		/* Check for compatible Magento Version */
		$result = false;
		if ($maintained = $this->getMaintainedVersion()) {
			$result = $this->isMajorVersion($maintained, '=');
		}
		return $result;
	}

	public function getMaintainedVersion()
	{
		$result = false;
		$xmlPath = Mage::getModuleDir('etc', 'Pisc_Downloadplus').DS.'version.xml';
		if (file_exists($xmlPath)) {
			$xmlObj = new Varien_Simplexml_Config($xmlPath);
			if ($xmlData = $xmlObj->getNode('magento')) {
				$result = $xmlData->asArray();
			}
		} else {
			Mage::throwException('DownloadPlus for Magento has detected a missing file: '.$xmlPath);
		}
		return $result;
	}
	
}
