<?php
/**
 * Downloadplus Link Image Thumbnail Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Link_Image_Thumbnail extends Mage_Catalog_Model_Product_Image
{

	public function setBaseFile($file)
	{
		$this->_isBaseFilePlaceholder = false;

		if (($file) && (0 !== strpos($file, '/', 0))) {
			$file = '/' . $file;
		}
		$baseDir = Mage::getSingleton('downloadplus/link_image')->getBaseMediaPath('image');

		if ('/no_selection' == $file) {
			$file = null;
		}
		if ($file) {
			if ((!$this->_fileExists($baseDir . $file)) || !$this->_checkMemory($baseDir . $file)) {
				$file = null;
			}
		}
		if (!$file) {
			// check if placeholder defined in config
			$isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/{$this->getDestinationSubdir()}_placeholder");
			$configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;
			if ($isConfigPlaceholder && $this->_fileExists($baseDir . $configPlaceholder)) {
				$file = $configPlaceholder;
			} else {
				// replace file with skin or default skin placeholder
				$skinBaseDir     = Mage::getDesign()->getSkinBaseDir();
				$skinPlaceholder = "/images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg";
				$file = $skinPlaceholder;
				if (file_exists($skinBaseDir . $file)) {
					$baseDir = $skinBaseDir;
				} else {
					$baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
					if (!file_exists($baseDir . $file)) {
						$baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default', '_package' => 'base'));
					}
				}
			}
			$this->_isBaseFilePlaceholder = true;
		}

		$baseFile = $baseDir . $file;

		if ((!$file) || (!file_exists($baseFile))) {
			throw new Exception(Mage::helper('catalog')->__('Image file was not found.'));
		}

		$this->_baseFile = $baseFile;

		// build new filename (most important params)
		$path = array(
				Mage::getSingleton('downloadplus/link_image')->getBaseMediaPath(),
				'cache',
				Mage::app()->getStore()->getId(),
				$path[] = $this->getDestinationSubdir()
		);
		if((!empty($this->_width)) || (!empty($this->_height)))
			$path[] = "{$this->_width}x{$this->_height}";

		// add misk params as a hash
		$miscParams = array(
			($this->_keepAspectRatio  ? '' : 'non') . 'proportional',
			($this->_keepFrame        ? '' : 'no')  . 'frame',
			($this->_keepTransparency ? '' : 'no')  . 'transparency',
			($this->_constrainOnly ? 'do' : 'not')  . 'constrainonly',
			$this->_rgbToString($this->_backgroundColor),
			'angle' . $this->_angle,
			'quality' . $this->_quality
		);

		// if has watermark add watermark params to hash
		if ($this->getWatermarkFile()) {
			$miscParams[] = $this->getWatermarkFile();
			$miscParams[] = $this->getWatermarkImageOpacity();
			$miscParams[] = $this->getWatermarkPosition();
			$miscParams[] = $this->getWatermarkWidth();
			$miscParams[] = $this->getWatermarkHeigth();
		}

		$path[] = md5(implode('_', $miscParams));

		// append prepared filename
		$this->_newFile = implode('/', $path) . $file; // the $file contains heading slash

		return $this;
	}

}
