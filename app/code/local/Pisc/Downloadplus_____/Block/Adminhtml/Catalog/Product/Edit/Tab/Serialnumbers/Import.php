<?php
/**
 * Catalog Product Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Serialnumbers_Import extends Mage_Adminhtml_Block_Template
{

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('downloadplus/product/edit/serialnumbers/import.phtml');
	}

	/*
     * Returns the current Customer
     */
    public function getProduct()
    {
		return Mage::registry('current_product');
    }

	/**
	 * Prepare block Layout
	 */
	protected function _prepareLayout()
	{
	}

	/*
	* Returns Select-Box for Serialnumber Pool setting
	*/
	public function getOptionSerialnumberPoolHtml($blockId, $blockName, $value)
	{
		$block = $this->getLayout()->createBlock('core/html_select');
		$block->setId($blockId);
		$block->setName($blockName);
		$block->setOptions(Mage::getModel('downloadplus/system_config_source_download_settings_serialnumber_product_import_pool')->toOptionArray(false, $this->getProduct()));
		$block->setValue($value);
	
		return $block->_toHtml();
	}
	
	/*
	 * Returns Select-Box for Serialnumber Product Attribute
	 */
	public function getAttributeSelectBox($attributeCode, $name, $id, $label = null, $defaultSelect = null, $extraParams = null)
	{
		$options = Array();
		$attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attributeCode);
		if ($attribute->usesSource()) {
			$options = $attribute->getSource()->getAllOptions(false);
			if ($label) {
				array_unshift($options, array('label' => $label, 'value' => ''));
			}
		}
	
		$select = Mage::app()->getLayout()->createBlock('core/html_select')
					->setName($name)
					->setId($id)
					->setValue($defaultSelect)
					->setExtraParams($extraParams)
					->setOptions($options);
		
		if ($label) {
			$select->setTitle($label);
		}
		
		return $select->getHtml();
	}	
	
}
