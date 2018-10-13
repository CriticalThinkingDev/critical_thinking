<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product View Downloads block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.3
 */

class Pisc_Downloadplus_Block_Product_View_Downloads extends Mage_Core_Block_Template
{

	protected $_product = null;
	protected $_collection = null;

	public function __construct()
	{
		parent::__construct();
	}

	protected function __beforeToHtml()
	{
		if (!$this->getTemplate()) {
			$this->setTemplate('downloadplus/product/additional/downloads.phtml');
		}
		return parent::__beforeToHtml();
	}

	/*
	 * Returns the associated Product Object
	 */
	public function getProduct()
	{
		if (!$this->_product) {
			if ($id = $this->getProductSku()) {
				$this->_product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->loadByAttribute('sku', $id);
			} else {
				$this->_product = Mage::getModel('catalog/product');

				if ($id = $this->getProductId()) {
					$this->_product->load($id);
				} else {
					if ($id = $this->getRequest()->getParam('id', 0)) { $this->_product->load($id); }
				}
			}
		}
		
        return $this->_product;
	}

	public function setProduct($product)
	{
		$this->_product = $product;
		$this->_collection = null;
		return $this;
	}
	
	public function setProductId($id)
	{
		if ($this->getData('product_id')!=$id) {
			$this->_collection = null;
		}
		$this->setData('product_id', $id);
		return $this;
	}

	public function setProductSku($id)
	{
		if ($this->getData('product_sku')!=$id) {
			$this->_collection = null;
		}
		$this->setData('product_sku', $id);
		return $this;
	}

	public function setAttributeCode($id)
	{
		if ($this->getData('attribute_code')!=$id) {
			$this->_collection = null;
		}
		$this->setData('attribute_code', $id);
		return $this;
	}

	public function setAttributeValue($id)
	{
		if ($this->getData('attribute_value')!=$id) {
			$this->_collection = null;
		}
		$this->setData('attribute_value', $id);
		return $this;
	}
	
	/*
	 * Returns boolean if this Product has Download Links
	 */
	public function hasLinks()
	{
		$collection = $this->getLinks();
		$result = !empty($collection);
		return $result;
	}

	/*
	 * Returns collection of related Product Download Links
	 */
	public function getLinks()
	{
		if (!$this->_collection) {
			if ($this->getProduct()) {
				if ($this->getAttributeCode() && $this->getAttributeValue()) {
					$this->_collection = Mage::getModel('downloadplus/link_product_item')->getCollection()
											->addAttributeFilter($this->getAttributeCode(), $this->getAttributeValue())
											->getByProductId($this->getProduct()->getId(), Mage::app()->getStore());
				} else {
					$this->_collection = Mage::getModel('downloadplus/link_product_item')->getCollection()
											->getByProductId($this->getProduct()->getId(), Mage::app()->getStore());
				}
			}
		}
		
		return $this->_collection;
	}

	/*
	 * Returns link to download URL
	 */
	public function getDownloadUrl($link)
	{
		$result = '';
		if ($link && $link->getId()) {
			$config = Mage::getModel('downloadplus/config');
			$params = array('id' => $link->getId());
			if ($config->isDownloadForceSecure()) {
				$params['_secure'] = true;
			}
			$result = $this->getUrl('downloadable/download/product', $params);
		}
		return $result;
	}

}
