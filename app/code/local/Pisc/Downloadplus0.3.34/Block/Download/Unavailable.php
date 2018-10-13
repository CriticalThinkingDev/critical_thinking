<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product Link Unavailable block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.3
 */

class Pisc_Downloadplus_Block_Download_Unavailable extends Mage_Core_Block_Template
{

	protected $_product = null;
	protected $_link = null;
	protected $_sample = null;
	protected $_linksample = null;
	protected $_detail = null;

	public function __construct()
	{
		// Load depedencies
		Mage::getModel('downloadplus/config');
		
		$this->_product = Mage::getModel('catalog/product');
		$this->_link = Mage::getModel('downloadable/link');
		$this->_sample = Mage::getModel('downloadable/sample');
		$this->_detail = Mage::getModel('downloadplus/download_detail');

        if ($id = $this->getRequest()->getParam('link', 0)) {
	        $this->_link = Mage::getModel('downloadable/link_purchased_item')->load($id, 'link_hash');
	        if ($this->_link->getId()) {
	        	$this->_product = Mage::getModel('catalog/product')->load($this->_link->getProductId());
	        }
        }
        if ($id = $this->getRequest()->getParam('linksample', 0)) {
	        $this->_linksample = Mage::getModel('downloadable/link')->load($id);
	        if ($this->_linksample->getId()) {
	        	$this->_product = Mage::getModel('catalog/product')->load($this->_link->getProductId());
	        }
        }
        if ($id = $this->getRequest()->getParam('sample', 0)) {
	        $this->_sample = Mage::getModel('downloadable/sample')->load($id);
	        if ($this->_sample->getId()) {
	        	$this->_product = Mage::getModel('catalog/product')->load($this->_sample->getProductId());
	        }
        }

        $config = Mage::getModel('downloadplus/config');
        if ($this->_link && $this->_link->getId()) {
        	if ($config->getDownloadableDeliveryProductBehaviour()==Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_LATEST) {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByLinkId($this->_link->getId());
        	} else {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK.$this->_link->getLinkFile());
        	}
        }
        if ($this->_linksample && $this->_linksample->getId()) {
        	if ($config->getDownloadableDeliveryProductBehaviour()==Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_LATEST) {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByLinkSampleId($this->_linksample->getId());
        	} else {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK_SAMPLE.$this->_link->getLinkFile());
        	}
        }
        if ($this->_sample && $this->_sample->getId()) {
        	if ($config->getDownloadableDeliveryProductBehaviour()==Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_LATEST) {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadBySampleId($this->_sample->getId());
        	} else {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_SAMPLE.$this->_sample->getSampleFile());
        	}
        }

		parent::__construct();
	}

	protected function _toHtml()
	{
		return parent::_toHtml();
	}

	/*
	 * Returns the applicable license text
	 */
	function getLicense()
	{
		$config = Mage::getModel('downloadplus/config');
		return $config->getDownloadableProductDefaultLicense();
	}

	/*
	 * Returns the requested Download URL
	 */
	function getDownloadUrl()
	{
		$base = $this->getBaseUrl();
		$base = substr_replace($base, '', -1);
		$request = $this->getRequest();
		return $base.$request->getOriginalPathInfo();
	}

	/*
	 * Returns the Form Action
	 */
	function getAction()
	{
		$base = $this->getBaseUrl();
		$base = substr_replace($base, '', -1);
		$request = $this->getRequest();
		return $base.$request->getOriginalPathInfo();
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
	 * Returns the associated Sample Object
	 */
	function getSample()
	{
        return $this->_sample;
	}

	/*
	 * Returns the associated Detail Object
	 */
	function getDetail()
	{
        return $this->_detail;
	}

	/*
	 * Returns the associated Link Extension Object
	 */
	function getLinkExtension()
	{
		$result = null;
		if ($this->_link) {
			$result = Mage::getModel('downloadplus/link_purchased_item_extension')->loadByPurchasedLink($this->_link);
			if (!$result->getId()) { $result = null; }
		}
		return $result;
	}
	
	/*
	 * Returns the related download link id
	 */
	function getLinkId()
	{
		return $this->getRequest()->getParam('link', 0);
	}

	/*
	 * Returns the related download link id
	 */
	function getLinkSampleId()
	{
		return $this->getRequest()->getParam('linksample', 0);
	}
	
	/*
	 * Returns the related download link id
	 */
	function getSampleId()
	{
		return $this->getRequest()->getParam('sample', 0);
	}

	/*
	 * Returns the Filename from a Path
	 */
	function getFilename($file)
	{
		$filename = substr($file, strrpos($file,'/')+1, strlen($file)-strrpos($file,'/'));
		return $filename;
	}

}
