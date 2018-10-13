<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product Link Samples License block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_License_Linksamples extends Mage_Core_Block_Template
{

	protected $_product;
	protected $_link;
	protected $_detail;

	public function __construct()
	{
		// Load dependencies
		Mage::getModel('downloadplus/download_detail');
		
        $id = $this->getRequest()->getParam('link_id', 0);
        $archiveId = $this->getRequest()->getParam('archive', false);

        $this->_link = Mage::getModel('downloadplus/link')->load($id);
        if ($this->_link->getId()) {
        	$this->_product = Mage::getModel('catalog/product')->load($this->_link->getProductId());
        } else {
        	$this->_product = Mage::getModel('catalog/product');
        }

        if ($archiveId) {
        	$this->_detail = Mage::getModel('downloadplus/download_detail')->load($archiveId);
        } else {
        	if (Mage::getModel('downloadplus/config')->isDownloadableDeliveryProductLatest()) {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByLinkSampleId($this->_link->getLinkId());
        	} else {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK_SAMPLE.$this->_link->getSampleFile());
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
		return $config->getDownloadableProductSampleDefaultLicense();
	}

	/*
	 * Returns the requested Download URL
	 */
	function getDownloadUrl()
	{
		$config = Mage::getModel('downloadplus/config');
		$params = array('link_id' => $this->_link->getLinkId());

		if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/linkSample', $params);
	}

	/*
	 * Returns the URL to the Release History
	 */
	function getArchiveUrl()
	{
		$config = Mage::getModel('downloadplus/config');
		$params = array('link_id' => $this->_link->getLinkId());

		if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/archive/linkSample', $params);
	}

	/*
	 * Returns if this Download has archived items
	 */
	function hasArchive()
	{
		return Mage::getModel('downloadplus/download_detail')
					->loadByLinkSampleId($this->_link->getLinkId())
					->hasArchive();
	}

	/*
	 * Returns the Download Detail
	 */
	function getDetail()
	{
		return $this->_detail;
	}

	/*
	 * Returns the Form Action
	 */
	function getAction()
	{
		$config = Mage::getModel('downloadplus/config');
		$params = array('link_id' => $this->_link->getLinkId());

		if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/linkSample', $params);
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
     * Returns a unique Form Key
     */
	public function getFormKey()
    {
    	return Mage::getSingleton('core/session')->getFormKey();
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
