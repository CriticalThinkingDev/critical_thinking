<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Log model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.1
 */
class Pisc_Downloadplus_Model_Type_File extends Mage_Core_Model_Abstract
{

	protected $_path = false;
	protected $_pathfile = false;
	protected $_file = null;
	protected $_size = false;
	protected $_timestamp = false;
	protected $_exists = false;

	protected $_title = null;
	protected $_product = null;

	/*
	 * Initialize
	 */
	protected function init()
	{
		$this->_path = false;
		$this->_file = null;
		$this->_size = false;
		$this->_timestamp = false;
		$this->_exists = false;
		$this->_title = null;
		$this->_product = null;

		return $this;
	}

	/*
	 * Loads a file resource from path and file
	 */
	public function loadResource($path, $file)
	{
		$resource = Mage::helper('downloadable/file')->getFilePath($path, $file);

		// Replace forwards Slashes with Directory Separater
		$resource = str_replace("/", DS, $resource);

		if (file_exists($resource)) {
			$this->_path = $path;
			$this->_pathfile = $resource;
			$this->_file = $file;
			$this->_size = filesize($resource);
			$this->_timestamp = filemtime($resource);
			$this->_exists = true;
		} else {
			$this->init();
		}

		return $this;
	}

	/*
	 * Returns if file exists
	 */
	public function exists()
	{
		return $this->_exists;
	}

	/*
	 * Loads a file resource from a Link
	 */
	public function loadByLink($link)
	{
		if ($link->getLinkType()==Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
			$this->_title = $this->getLinkTitle($link);
			$this->_product = Mage::getModel('catalog/product')->load($link->getProductId());
			return $this->loadResource(Mage_Downloadable_Model_Link::getBasePath(), $link->getLinkFile());
		}
		return $this->init();
	}

	/*
	 * Loads a file resource from a Link
	 */
	public function loadByLinkSample($link)
	{
		if ($link->getLinkType()==Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
			$this->_title = $this->getLinkTitle($link);
			$this->_product = Mage::getModel('catalog/product')->load($link->getProductId());
			return $this->loadResource(Mage_Downloadable_Model_Link::getBaseSamplePath(), $link->getSampleFile());
		}
		return $this->init();
	}

	/*
	 * Returns a Link Object from a LinkId
	 */
	public function getLinkById($id)
	{
		return Mage::getModel('downloadable/link')->load($id);
	}

	/*
	 * Returns a Link Object from a LinkSampleId
	 */
	public function getLinkSampleById($id)
	{
		return Mage::getModel('downloadplus/link')->load($id);
	}

	/*
	 * Loads a file resource from a Sample
	 */
	public function loadBySample($sample)
	{
		if ($sample->getSampleType()==Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
			$this->_title = $this->getSampleTitle($sample);
			$this->_product = Mage::getModel('catalog/product')->load($sample->getProductId());
			return $this->loadResource(Mage_Downloadable_Model_Sample::getBasePath(), $sample->getSampleFile());
		}
		return $this->init();
	}

	/*
	 * Returns a Sample Object from a SampleId
	 */
	public function getSampleById($id)
	{
		return Mage::getModel('downloadable/sample')->load($id);
	}

	/*
	 * Returns the Title of a download link
	 */
	protected function getLinkTitle($link)
	{
		$product = Mage::getModel('catalog/product')->load($link->getProductId());
        $links = $product->getTypeInstance(true)->getLinks($product);
        if (count($links)>0) {
        	foreach ($links as $item) {
        		if ($item->getId()==$link->getId()) {
        			return $item->getTitle();
        		}
        	}
        }
        return '';
	}

	/*
	 * Returns the Title of a download sample
	 */
	protected function getSampleTitle($sample)
	{
		$product = Mage::getModel('catalog/product')->load($sample->getProductId());
        $samples = $product->getTypeInstance(true)->getSamples($product);
        if (count($samples)>0) {
        	foreach ($samples as $item) {
        		if ($item->getId()==$sample->getId()) {
        			return $item->getTitle();
        		}
        	}
        }
        return '';
	}

	/*
	 * Returns the current files name
	 */
	public function getFile()
	{
		return $this->_file;
	}

	/*
	 * Returns the current files full path
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/*
	 * Returns the current files full path and name
	 */
	public function getPathFile()
	{
		return $this->_pathfile;
	}

	/*
	 * Returns the current files size
	 */
	public function getSize()
	{
		return $this->_size;
	}

	/*
	 * Returns the currents files timestamp
	 */
	public function getTimeStamp()
	{
		return $this->_timestamp;
	}

	/*
	 * Returns the files download title
	 */
	public function getTitle()
	{
		return $this->_title;
	}

	/*
	 * Returns the files product object
	 */
	public function getProduct()
	{
		return $this->_product;
	}

}