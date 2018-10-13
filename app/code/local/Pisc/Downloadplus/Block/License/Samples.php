<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product Samples License block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.3
 */

class Pisc_Downloadplus_Block_License_Samples extends Mage_Core_Block_Template
{

	protected $_product;
	protected $_sample;
	protected $_detail;
	protected $_config;
	
	public function __construct()
	{
		// Load depedencies
		Mage::getModel('downloadplus/download_detail');
		
        $sampleId = $this->getRequest()->getParam('sample_id', 0);
        $archiveId = $this->getRequest()->getParam('archive', false);

        $this->_sample = Mage::getModel('downloadable/sample')->load($sampleId);
        $this->_product = Mage::getModel('catalog/product');
        
        if ($this->_sample->getId()) {
        	$this->_product->load($this->_sample->getProductId());
        }

        if ($archiveId) {
        	$this->_detail = Mage::getModel('downloadplus/download_detail')->load($archiveId);
        } else {
        	if (Mage::getModel('downloadplus/config')->isDownloadableDeliveryProductLatest()) {
        		$this->_detail = Mage::getModel('downloadplus/download_detail')->loadBySampleId($this->_sample->getSampleId());
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

	private function getConfig()
	{
		if (!$this->_config) {
			$this->_config = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore());
		}
		return $this->_config;
	}
	
    /*
     * Returns a unique Form Key
     */
	public function getFormKey()
    {
    	return Mage::getSingleton('core/session')->getFormKey();
    }

	/*
	 * Returns the applicable license text
	 */
	function getLicense()
	{
		$config = Mage::getModel('downloadplus/config');
		return $this->getConfig()->getDownloadableSampleDefaultLicense();
	}

	/*
	 * Returns the requested Download URL
	 */
	function getDownloadUrl()
	{
		$params = array('sample_id' => $this->_sample->getSampleId());
		if ($this->getConfig()->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/sample', $params);
	}

	/*
	 * Returns the URL to the Release History
	 */
	function getArchiveUrl()
	{
		$params = array('sample_id' => $this->_sample->getSampleId());
		if ($this->getConfig()->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/archive/samples', $params);
	}

	/*
	 * Returns if this Download has archived items
	 */
	function hasArchive()
	{
		$result = Mage::getModel('downloadplus/download_detail')
					->loadBySampleId($this->_sample->getSampleId())
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
		$params = array('sample_id' => $this->_sample->getSampleId());
		if ($this->getConfig()->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/sample', $params);
	}

	/*
	 * Returns the associated Product Object
	 */
	function getProduct()
	{
        return $this->_product;
	}

	/*
	 * Returns the associated Sample Object
	 */
	function getSample()
	{
        return $this->_sample;
	}

	/*
	 * Returns the Sample Title
	 */
	function getSampleTitle()
	{
		$product = $this->getProduct();

        $samples = $product->getTypeInstance(true)->getSamples($product);
        if (count($samples)>0) {
        	foreach ($samples as $sample) {
        		if ($sample->getId()==$this->getSample()->getId()) {
        			return $sample->getTitle();
        		}
        	}
        }

        return;
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
