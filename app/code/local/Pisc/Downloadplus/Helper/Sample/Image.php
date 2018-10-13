<?php
/**
 * Downloadplus Image Helper
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Helper_Sample_Image extends Mage_Catalog_Helper_Image
{
	
	public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null)
	{
		parent::init($product, $attributeName, $imageFile);
		
		$this->_setModel(Mage::getModel('downloadplus/sample_image_'.$attributeName));
		$this->_getModel()->setDestinationSubdir($attributeName);
		
		if (!$imageFile) {
			// add for work original size
			$this->_getModel()->setBaseFile($this->getProduct()->getData($this->_getModel()->getDestinationSubdir()));
		}
		
		return $this;
	}
	
	public function getNewFile()
	{
		$file = $this->_getModel()->setBaseFile($this->getImageFile())->getNewFile();
		return $file;
	}

	public function setSize($width, $height)
	{
		$this->_getModel()->setWidth($width)->setHeight($height);
		return $this;
	}
	
}