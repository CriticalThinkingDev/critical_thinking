<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

class Pisc_Downloadplus_Model_System_Config_Backend_Catalog_Product_Filerelation
{

	const IS_CODELENGTH = 2;
	const IS_PLEASE_SELECT = 'X.';
	const IS_REMOVE_ASSOCIATION = 'RA';
	const IS_LINK = 'L.';
	const IS_LINKSAMPLE = 'LS';
	const IS_SAMPLE = 'S.';

	protected $_product = null;
	protected $_detail = null;

	public function setProduct($product)
	{
		$this->_product = $product;
		return $this;
	}

	public function getProduct()
	{
		return $this->_product;
	}

	public function setDetail($detail)
	{
		$this->_detail = $detail;
		return $this;
	}

	public function getDetail()
	{
		return $this->_detail;
	}

    public function getValue()
    {
    	$result = self::IS_PLEASE_SELECT;

    	if ($file = $this->getDetail()) {
	    	if ($file->getLinkId() && $file->getLinkId()!==0) { $result = self::IS_LINK.$file->getLinkId(); }
	    	if ($file->getLinkSampleId() && $file->getLinkSampleId()!==0) { $result = self::IS_LINKSAMPLE.$file->getLinkSampleId(); }
	    	if ($file->getSampleId() && $file->getSampleId()!==0) { $result = self::IS_SAMPLE.$file->getSampleId(); }
    	}

    	return $result;
    }

    public function toOptionArray()
    {
    	$result = Array();
    	$result[] = array(
    					'value'=>self::IS_PLEASE_SELECT,
    					'label'=>Mage::helper('downloadplus')->__('-- Please select --')
    				);
    	$result[] = array(
    					'value'=>self::IS_REMOVE_ASSOCIATION,
    					'label'=>Mage::helper('downloadplus')->__('(Remove Association)')
    				);

    	// Link Titles
    	if ($this->getDetail() && $this->getDetail()->getBasePath()==Mage::getModel('downloadable/link')->getBasePath()) {
	    	$titles = Mage::getModel('downloadplus/download_titles')
	    				->getResource()
	    				->addProductToFilter($this->getProduct())
	    				->getAllLinkTitles();

	    	foreach ($titles as $title) {
	    		$result[] = array(
	    						'value'=>self::IS_LINK.$title['link_id'],
	    						'label'=>Mage::helper('downloadplus')->__('(Link)').' '.$title['title']
	    					);
	    	}
    	}

    	// Link Sample Titles
    	if ($this->getDetail() && $this->getDetail()->getBasePath()==Mage::getModel('downloadable/link')->getBaseSamplePath()) {
	    	$titles = Mage::getModel('downloadplus/download_titles')
	    				->getResource()
	    				->addProductToFilter($this->getProduct())
	    				->getAllLinkTitles();

	    	foreach ($titles as $title) {
	    		$result[] = array(
	    						'value'=>self::IS_LINKSAMPLE.$title['link_id'],
	    						'label'=>Mage::helper('downloadplus')->__('(Link Sample)').' '.$title['title']
	    					);
	    	}
    	}

    	// Sample Titles
    	if ($this->getDetail() && $this->getDetail()->getBasePath()==Mage::getModel('downloadable/sample')->getBasePath()) {
	    	$titles = Mage::getModel('downloadplus/download_titles')
	    				->getResource()
	    				->addProductToFilter($this->getProduct())
	    				->getAllSampleTitles();

	    	foreach ($titles as $title) {
	    		$result[] = array(
	    						'value'=>self::IS_SAMPLE.$title['sample_id'],
	    						'label'=>Mage::helper('downloadplus')->__('(Sample)').' '.$title['title']
	    					);
	    	}
    	}

        return $result;
    }

}
