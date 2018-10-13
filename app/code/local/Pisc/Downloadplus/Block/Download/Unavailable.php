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
 * @version		0.1.6
 */

class Pisc_Downloadplus_Block_Download_Unavailable extends Mage_Core_Block_Template
{

	protected $_product = null;
	protected $_link = null;
	protected $_sample = null;
	protected $_linksample = null;
	protected $_detail = null;
	protected $_additional = null;
	protected $_bonus = null;
	protected $_config;
	
	
	public function __construct()
	{
		// Load depedencies
		Mage::getModel('downloadplus/config');
		
        if ($id = $this->getRequest()->getParam('link', 0)) {
	        $this->_link = Mage::getModel('downloadable/link_purchased_item')->load($id, 'link_hash');
	        if ($this->_link->getId()) {
	        	$this->_product = Mage::getModel('catalog/product')->load($this->_link->getProductId());
	        }
        }
        if ($id = $this->getRequest()->getParam('linksample', 0)) {
	        $this->_linksample = Mage::getModel('downloadplus/link')->load($id);
	        if ($this->_linksample->getId()) {
	        	$this->_product = Mage::getModel('catalog/product')->load($this->_link->getProductId());
	        }
        }
        if ($id = $this->getRequest()->getParam('sample', 0)) {
	        $this->_sample = Mage::getModel('downloadplus/sample')->load($id);
	        if ($this->_sample->getId()) {
	        	$this->_product = Mage::getModel('catalog/product')->load($this->_sample->getProductId());
	        }
        }

        if ($id = $this->getRequest()->getParam('additional', 0)) {
        	$this->_additional = Mage::getModel('downloadplus/link_product_item')->load($id);
        	if ($this->_additional->getId()) {
        		$this->_product = Mage::getModel('catalog/product')->load($this->_additional->getProductId());
        	}
        }

        if (($id = $this->getRequest()->getParam('bonus', 0)) && Mage::helper('downloadplus')->existsDownloadplusBonus()) {
        	$this->_bonus = Mage::getModel('downloadplusbonus/link_purchased_bonus_item')->load($id, 'link_hash');
        	if ($this->_bonus->getId()) {
        		$this->_product = Mage::getModel('catalog/product')->load($this->_bonus->getProductId());
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
        if ($this->_additional && $this->_additional->getId()) {
       		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByLinkProductItemId($this->_additional->getId());
        }
        if (Mage::helper('downloadplus')->existsDownloadplusBonus() && $this->_bonus && $this->_bonus->getId()) {
        	$this->_detail = $this->_bonus->getDownloadDetail();
        }
        
		parent::__construct();
	}

	protected function _toHtml()
	{
		return parent::_toHtml();
	}

	private function getConfig()
	{
		if (!$this->_config) {
			$this->_config = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore());
		}
		return $this->_config;
	}
	
	/*
	 * Returns the applicable license text
	 */
	public function getLicense()
	{
		return $this->getConfig()->getDownloadableProductDefaultLicense();
	}

	/*
	 * Returns the requested Download URL
	 */
	public function getDownloadUrl()
	{
		$base = $this->getBaseUrl();
		$base = substr_replace($base, '', -1);
		$request = $this->getRequest();
		return $base.$request->getOriginalPathInfo();
	}

	/*
	 * Returns the Form Action
	 */
	public function getAction()
	{
		$base = $this->getBaseUrl();
		$base = substr_replace($base, '', -1);
		$request = $this->getRequest();
		return $base.$request->getOriginalPathInfo();
	}

	/*
	 * Returns the associated Product Object
	 */
	public function getProduct()
	{
        return $this->_product;
	}

	public function getDetail()
	{
		return $this->_detail;
	}
	
	/*
	 * Returns the Filename from a Path
	 */
	public function getFilename($file)
	{
		$filename = substr($file, strrpos($file,'/')+1, strlen($file)-strrpos($file,'/'));
		return $filename;
	}

	public function getLinkTitle()
	{
		if ($this->_link) { return $this->_link->getLinkTitle(); }
		if ($this->_linksample) { return $this->_link->setStoreId(Mage::helper('downloadplus')->getStoreId())->getLinkTitle(); }
		if ($this->_sample) { return $this->_sample->setStoreId(Mage::helper('downloadplus')->getStoreId())->getSampleTitle(); }
		if ($this->_additional) { return $this->_sample->setStoreId(Mage::helper('downloadplus')->getStoreId())->getLinkTitle(); }
		if ($this->_bonus) { return $this->_bonus->getLinkTitle(); }
		
		return null;		
	}
	
	public function getLinkExtension()
	{
		if ($this->_link) { return $this->_link->getExtension(); }
		
		return null;
	}
	
}
