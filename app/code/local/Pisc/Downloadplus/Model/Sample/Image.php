<?php
/**
 * Downloadplus Sample Thumbnail Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Sample_Image extends Mage_Core_Model_Abstract
{
	protected $_eventPrefix = 'downloadplus_sample_image';

	protected $_width = '75';
	protected $_height = '75';
	
	public static function getBaseTmpPath($product=null)
	{
		$path = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'sample_image';
		if ($product instanceof Mage_Catalog_Model_Product) {
			$path.= DS . $product->getId();
		} elseif (is_numeric($product)) {
			$path.= DS . $product;
		}
	
		return $path;
	}
	
	public static function getBasePath($product=null)
	{
		$path = Mage::getBaseDir('media') . DS . 'catalog' .DS. 'downloadable' . DS . 'sample' .DS. 'image';
		if ($product instanceof Mage_Catalog_Model_Product) {
			$path.= DS . $product->getId();
		} elseif (is_numeric($product)) {
			$path.= DS . $product;
		}
	
		return $path;
	}
	
	public static function getBaseMediaPath($type=null)
	{
		$path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'downloadable' .DS. 'sample';
		if ($type) {
			$path.= DS . $type;
		}
		return $path;
	}
	
	public static function getBaseMediaUrl($type=null)
	{
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/catalog/downloadable/sample';
		if ($type) {
			$url.='/'.$type;
		}
		return $url;
	}
	
	protected function _getImageHelper()
	{
		return Mage::helper('downloadplus/sample_image');
	}
	
	public function getImageThumbnailUrl($width = null, $height = null)
	{
		$url = null;
		if ($this->getSample() && $this->getSample()->getImageFile()) {
				
			if (empty($width) || empty($height)) {
				$size = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore())->getImageThumbnailSize();
				$width = $size[0];
				$height = $size[1];
			}
	
			$file = $this->getSample()->getImageFile();
			if (!empty($file) && $this->getSample()->getProductId()) {
				$product = Mage::getModel('catalog/product')->load($this->getSample()->getProductId());
				$url = (string)$this->_getImageHelper()
				->init($product, 'thumbnail', $file)
				->resize($width, $height);
			}
	
		}
		return $url;
	}
	
	public function getImageUrl()
	{
		$url = null;
		if ($this->getSample() && $this->getSample()->getImageFile()) {
			$url = $this->getBaseMediaUrl('image').$this->getSample()->getImageFile();
		}
		return $url;
	}
	
	public function remove()
	{
		$file = $this->getSample()->getImageFile();
		if (!empty($file) && $this->getSample()->getProductId()) {
			$product = Mage::getModel('catalog/product')->load($this->getSample()->getProductId());
				
			$size = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore())->getImageThumbnailSize();
			$width = $size[0];
			$height = $size[1];
	
			$thumbnailFile = (string)$this->_getImageHelper()
			->init($product, 'thumbnail', $file)
			->setSize($width, $height)
			->getNewFile();
	
			$imageFile = Mage::helper('downloadable/file')->getFilePath(
					$this->getBasePath(), $file
			);
	
			if (file_exists($imageFile)) {
				@unlink($file);
			}
			if (file_exists($thumbnailFile)) {
				@unlink($thumbnailFile);
			}
				
			$this->getSample()->setImageFile(null);
		}
	
		return $this;
	}
		
}