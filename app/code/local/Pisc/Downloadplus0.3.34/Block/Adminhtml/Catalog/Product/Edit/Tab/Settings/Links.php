<?php
/**
 * Catalog Product Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Settings_Links extends Mage_Adminhtml_Block_Template
{

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('downloadplus/product/edit/settings/links.phtml');
	}

	/*
     * Returns the current Customer
     */
    public function getProduct()
    {
		return Mage::registry('current_product');
    }

    /*
     * Returns the current related Downloadable Link Items
     */
    public function getDownloadableLinkItems()
    {
        $links = $this->getProduct()->getTypeInstance(true)->getLinks($this->getProduct());
    	return $links;
    }

    /*
     * Returns Select-Box for expire setting
     */
    public function getOptionExpireHtml($blockId, $blockName, $value)
    {
    	$block = $this->getLayout()->createBlock('core/html_select');
    	$block->setId($blockId);
    	$block->setName($blockName);
    	$block->setOptions(Mage::getModel('downloadplus/system_config_source_download_settings_expire')->toOptionArray());
    	$block->setValue($value);    	
    	
    	return $block->_toHtml();
    }

    /*
    * Returns Select-Box for Serialnumber Pool setting
    */
    public function getOptionSerialnumberPoolHtml($blockId, $blockName, $value)
    {
    	$block = $this->getLayout()->createBlock('core/html_select');
    	$block->setId($blockId);
    	$block->setName($blockName);
    	$block->setOptions(Mage::getModel('downloadplus/system_config_source_download_settings_serialnumber_pool')->toOptionArray(false, $this->getProduct()));
    	$block->setValue($value);
    	 
    	return $block->_toHtml();
    }
    
	/**
	 * Prepare block Layout
	 */
	protected function _prepareLayout()
	{
	}

}
