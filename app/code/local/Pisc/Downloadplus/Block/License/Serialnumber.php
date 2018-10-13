<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Product Download Serialnumber License block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_License_Serialnumber extends Mage_Core_Block_Template
{

	protected $_product;
	protected $_customer;
	protected $_serialnumber;
	protected $_config;

	public function __construct()
	{
        $id = $this->getRequest()->getParam('id', 0);

        $this->_product = Mage::getModel('catalog/product');
        $this->_customer = Mage::getModel('customer/customer');
        $this->_serialnumber = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->load($id, 'serial_hash');
        if ($this->_serialnumber->getId()) {
        	$this->_product = $this->_serialnumber->getProduct();
        	$this->_customer = $this->_serialnumber->getCustomer();
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
		$config = Mage::getModel('downloadplus/config');
		return $this->getConfig()->getDownloadableSerialnumberDownloadDefaultLicense();
	}

	/*
	 * Returns the requested Download URL
	 */
	function getDownloadUrl()
	{
		$params = array('id' => $this->_serialnumber->getSerialHashId());
		if ($this->getConfig()->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/serialnumber', $params);
	}

	/*
	 * Returns the Form Action
	 */
	function getAction()
	{
		$params = array('id' => $this->_serialnumber->getSerialHash());
		if ($this->getConfig()->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

        return $this->getUrl('downloadable/download/serialnumber', $params);
	}

	/*
	 * Returns the associated Product Object
	 */
	function getProduct()
	{
        return $this->_product;
	}

	/*
	 * Returns the associated Customer Object
	 */
	function getCustomer()
	{
        return $this->_customer;
	}

    /*
     * Returns a unique Form Key
     */
	public function getFormKey()
    {
    	return Mage::getSingleton('core/session')->getFormKey();
    }

}
