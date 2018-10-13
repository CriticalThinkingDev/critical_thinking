<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product View History block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_Product_View_History extends Mage_Core_Block_Template
{

	protected $_product = null;
	protected $_link = null;
	protected $_sample = null;
	protected $_sort = null;
	protected $_activeCollection = null;
	protected $_historyCollection = null;

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
	 * Returns current Store
	 */
	public function getStore()
	{
		return Mage::app()->getStore();
	}

	/*
	 * Sets Link
	 */
	public function setLink($link)
	{
		$this->_link = $link;
		$this->_sample = null;

		return $this;
	}

	/*
	 * Sets Sample
	 */
	public function setSample($sample)
	{
		$this->_link = null;
		$this->_sample = $sample;

		return $this;
	}

	/*
	 * Sets a Sort Condition
	 */
	public function setSort($sort)
	{
		$this->_sort = $sort;

		return $this;
	}

    public function hasLinks()
    {
        return Mage::getModel('downloadable/product_type')->hasLinks($this->getProduct());
    }

	/*
	 * Returns the Link Collection
	 */
	public function getLinks()
	{
		$result = Mage::getModel('downloadable/product_type')->getLinks($this->getProduct());
        return $result;
	}

	/*
	 * Returns the Title for the Links Section
	 */
    public function getLinksTitle()
    {
        if ($this->getProduct()->getLinksTitle()) {
            return $this->getProduct()->getLinksTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    public function hasSamples()
    {
        return Mage::getModel('downloadable/product_type')->hasLinks($this->getProduct());
    }

    /*
     * Returns the Samples Collection
     */
    public function getSamples()
    {
		$result = Mage::getModel('downloadable/product_type')->getSamples($this->getProduct());
        return $result;
    }

    /*
     * Return title of samples section
     */
    public function getSamplesTitle()
    {
        if ($this->getProduct()->getSamplesTitle()) {
            return $this->getProduct()->getSamplesTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Sample::XML_PATH_SAMPLES_TITLE);
    }

    /*
     * Returns the Detail for a Link-Id
     */
    public function getLinkDetail($link_id)
    {
    	$result = Mage::getModel('downloadplus/download_detail')->loadByLinkId($link_id, $this->getStore());
    	return $result;
    }

    /*
     * Returns the Detail for a Link-Id
     */
    public function getSampleDetail($sample_id)
    {
    	$result = Mage::getModel('downloadplus/download_detail')->loadBySampleId($sample_id, $this->getStore());
    	return $result;
    }

	/*
	 * Returns the File Active Collection
	 */
	public function getActiveCollection()
	{
		$collection = Array();

		if (is_null($this->_activeCollection)) {
			$collection = Mage::getModel('downloadplus/download_detail')->getCollection()
							->addProductToFilter($this->getProduct())
							->getActiveFiles();
			// Update collection for current store
			if (Mage::app()->isSingleStoreMode()) {
				$this->_activeCollection = $collection;
			} else {
				$this->_activeCollection = Array();
				foreach ($collection as $item) {
					if ($file = Mage::getModel('downloadplus/download_detail')->loadByFile($item->getFile(), $this->getStore())) {
						$this->_activeCollection[] = $file;
					}
				}
			}
		}

		return $this->_activeCollection;
	}

	/*
	 * Returns the File History Collection
	 */
	public function getHistoryCollection()
	{
		$collection = Array();

		if (is_null($this->_historyCollection)) {
			if ($this->_link) {
				$collection = Mage::getModel('downloadplus/download_detail')->getCollection()
							->addLinkToFilter($this->_link->getLinkId())
							->addSort($this->_sort)
							->addVisibleToFilter(true)
							->getHistoricalFiles();
			}

			if ($this->_sample) {
				$collection = Mage::getModel('downloadplus/download_detail')->getCollection()
							->addSampleToFilter($this->_sample->getSampleId())
							->addSort($this->_sort)
							->addVisibleToFilter(true)
							->getHistoricalFiles();
			}

			// Update collection for current store
			if (Mage::app()->isSingleStoreMode()) {
				$this->_historyCollection = $collection;
			} else {
				$this->_historyCollection = Array();
				foreach ($collection as $item) {
					if ($file = Mage::getModel('downloadplus/download_detail')->loadByFile($item->getFile(), $this->getStore())) {
						$this->_historyCollection[] = $file;
					}
				}
			}
		}

		return $this->_historyCollection;
	}

}
