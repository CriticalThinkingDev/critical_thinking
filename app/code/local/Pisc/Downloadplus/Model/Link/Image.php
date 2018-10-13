<?php
/**
 * Downloadplus Link Thumbnail Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Link_Image extends Mage_Catalog_Model_Product_Image
{
	protected $_eventPrefix = 'downloadplus_link_image';
	
	protected $_width = '75';
	protected $_height = '75';
	
	public static function getBaseTmpPath($product=null)
	{
		$path = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'link_image';
		if ($product instanceof Mage_Catalog_Model_Product) {
			$path.= DS . $product->getId();
		} elseif (is_numeric($product)) {
			$path.= DS . $product;
		}
	
		return $path;
	}
	
	public static function getBasePath($product=null)
	{
		$path = Mage::getBaseDir('media') . DS . 'catalog' .DS. 'downloadable' . DS . 'link' .DS. 'image';
		if ($product instanceof Mage_Catalog_Model_Product) {
			$path.= DS . $product->getId();
		} elseif (is_numeric($product)) {
			$path.= DS . $product;
		}
	
		return $path;
	}

	public static function getBaseMediaPath($type=null)
	{
		$path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'downloadable' .DS. 'link';
		if ($type) {
			$path.= DS . $type;
		}
		return $path;
	}

	public static function getBaseMediaUrl($type=null)
	{
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/catalog/downloadable/link';
		if ($type) {
			$url.='/'.$type;
		}
		return $url;
	}
	
	protected function _getImageHelper()
	{
		return Mage::helper('downloadplus/link_image');
	}
	
	public function getImageThumbnailUrl($width = null, $height = null)
	{
		$url = null;
		if ($this->getLink() && $this->getLink()->getImageFile()) {
			
			if (empty($width) || empty($height)) {
				$size = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore())->getImageThumbnailSize();
				$width = $size[0];
				$height = $size[1];
			}
		
			$file = $this->getLink()->getImageFile();
			if (!empty($file) && $this->getLink()->getProductId()) {
				$product = Mage::getModel('catalog/product')->load($this->getLink()->getProductId());
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
		if ($this->getLink() && $this->getLink()->getImageFile()) {
			$url = $this->getBaseMediaUrl('image').$this->getLink()->getImageFile();
		}
		return $url;
	}

	public function remove()
	{
		$file = $this->getLink()->getImageFile();
		if (!empty($file) && $this->getLink()->getProductId()) {
			$product = Mage::getModel('catalog/product')->load($this->getLink()->getProductId());
			
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
			
			$this->getLink()->setImageFile(null);
		}

		return $this;
	}
	
}