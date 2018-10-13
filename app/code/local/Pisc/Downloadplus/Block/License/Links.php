<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product Links License block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.3
 */

class Pisc_Downloadplus_Block_License_Links extends Mage_Core_Block_Template
{

	protected $_product;
	protected $_link;
	protected $_detail;
	protected $_config;

	public function __construct()
	{
		// Load depedencies
		Mage::getModel('downloadplus/download_detail');
		
        $id = $this->getRequest()->getParam('id', 0);
        $archiveId = $this->getRequest()->getParam('archive', false);

        $this->_link = Mage::getModel('downloadable/link_purchased_item')->load($id, 'link_hash');
        $this->_product = Mage::getModel('catalog/product');
        
        if ($this->_link->getId()) {
        	$this->_product->load($this->_link->getProductId());
        }

        if ($archiveId) {
        	$this->_detail = Mage::getModel('downloadplus/download_detail')->load($archiveId);
        } else {
        	if (Mage::getModel('downloadplus/config')->isDownloadableDeliveryProductLatest()) {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByLinkId($this->_link->getLinkId());
        	} else {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadByFile(Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK.$this->_link->getLinkFile());
        	}
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
	function getLicense()
	{
		return $this->getConfig()->getDownloadableProductDefaultLicense();
	}

	/*
	 * Returns the requested Download URL
	 */
	function getDownloadUrl()
	{
		$params = array('id' => $this->_link->getLinkHash());
		if ($this->getConfig()->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/link', $params);
	}

	/*
	 * Returns the URL to the Release History
	 */
	function getArchiveUrl()
	{
		$params = array('id' => $this->_link->getLinkHash());
		if ($this->getConfig()->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/archive/purchased', $params);
	}

	/*
	 * Returns if this Download has archived items
	 */
	function hasArchive()
	{
		$result = Mage::getModel('downloadplus/download_detail')
					->loadByLinkId($this->_link->getLinkId())
					->hasArchive();

		return $result;
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
		$params = array('id' => $this->_link->getLinkHash());
		
		$archiveId = $this->getRequest()->getParam('archive', false);
		if ($archiveId) {
			$params['archive'] = $archiveId;
		}

		if ($this->getConfig()->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/link', $params);
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
