<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product Archive block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.3
 */

class Pisc_Downloadplus_Block_Download_Linksamples_Archive extends Mage_Core_Block_Template
{

	protected $_product = null;
	protected $_link = null;
	protected $_sort = null;

	public function __construct()
	{
        $id = $this->getRequest()->getParam('link_id', 0);

        $this->_link = Mage::getModel('downloadplus/link')->load($id);
        if ($this->_link->getId()) {
        	$this->_product = Mage::getModel('catalog/product')->load($this->_link->getProductId());
        } else {
        	$this->product = Mage::getModel('catalog/product');
        }

		parent::__construct();
	}

	protected function _toHtml()
	{
		return parent::_toHtml();
	}

	/*
	 * Returns the associated Product Object
	 */
	function getProduct()
	{
        return $this->_product;
	}

	/*
	 * Returns the associated Link Object
	 */
	function getLink()
	{
        return $this->_link;
	}

	/*
	 * Sets a Sort Condition
	 */
	function setSort($sort)
	{
		$this->_sort = $sort;

		return $this;
	}

	/*
	 * Returns the requested original Download URL
	 */
	function getOriginalDownloadUrl()
	{
		$config = Mage::getModel('downloadplus/config');
		$params = array('link_id' => $this->_link->getLinkId());

		if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/linkSample', $params);
	}

	/*
	 * Returns a Download Link for a particular archived file
	 */
	function getArchiveDownloadUrl($detail)
	{
		$config = Mage::getModel('downloadplus/config');
		$params = array('link_id' => $this->_link->getLinkId());

		if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

		if ($detail instanceof Pisc_Downloadplus_Model_Download_Detail) {
			$params['archive'] = $detail->getId();
		} else {
			$params['archive'] = $detail;
		}

		return $this->getUrl('downloadable/download/linkSample', $params);
	}

	/*
	 * Returns Download Details for the most recent download
	 */
	function getDetail()
	{
		$result = null;
		if ($this->_link && $this->_link->getLinkId()) {
			$result = Mage::getModel('downloadplus/download_detail')->loadByLinkSampleId($this->_link->getLinkId());
		}
		return $result;
	}
	
	/*
	 * Returns the File History Collection
	 */
	function getCollection()
	{
		$result = Mage::getModel('downloadplus/download_detail')->getCollection()
					->addLinkSampleToFilter($this->_link->getLinkId())
					->addSort($this->_sort)
					->getHistoricalFiles();
		return $result;
	}

}
