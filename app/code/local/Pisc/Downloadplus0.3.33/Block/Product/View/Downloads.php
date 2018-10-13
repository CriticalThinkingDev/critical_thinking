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
 * @version		0.1.0
 */

class Pisc_Downloadplus_Block_Product_View_Downloads extends Mage_Core_Block_Template
{

	protected $_product = null;

	public function __construct()
	{
        $id = $this->getRequest()->getParam('id', 0);

        $this->_product = Mage::getModel('catalog/product')->load($id);

		parent::__construct();
	}

	protected function _toHtml()
	{
		return parent::_toHtml();
	}

	/*
	 * Returns the associated Product Object
	 */
	public function getProduct()
	{
        return $this->_product;
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
		$collection = Array();
		if ($this->getProduct()) {
			$collection = Mage::getModel('downloadplus/link_product_item')->getCollection()->getByProductId($this->getProduct()->getId());
		}
		return $collection;
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
