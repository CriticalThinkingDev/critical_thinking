<?php
/**
* @category   Pisc
* @package    Pisc_DownloadPlus
* @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
*/

/**
 * Extending Core Adminhtml catalog product downloadable items tab links section
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.1
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links_Attributes extends Mage_Adminhtml_Block_Catalog_Form
{
	
	protected function _prepareForm()
	{
		$product = $this->getProduct();
		if (!$this->getAttributes()) {
			$this->setAttributes(Mage::helper('downloadplus')->getCustomDownloadableAttributes($product, 'link'));
		}
		$attributes = $this->getAttributes();
		
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix($this->getId());
		$form->setFieldNameSuffix($this->getName());
		$this->_createForm($attributes, $form);
		
		$this->setForm($form);
	}

	protected function _createForm($attributes, $form, $exclude=array())
	{
		$product = $this->getProduct();
		foreach ($attributes as $attribute) {
			if ( ($inputType = $attribute->getFrontend()->getInputType())
					&& !in_array($attribute->getAttributeCode(), $exclude)
					&& ('media_image' != $inputType) ) {

				$fieldType      = $inputType;
				$rendererClass  = $attribute->getFrontend()->getInputRendererClass();
				if (!empty($rendererClass)) {
					$fieldType  = $inputType . '_' . $attribute->getAttributeCode();
					$form->addType($fieldType, $rendererClass);
				}
	
				$element = $form->addField($attribute->getAttributeCode(), $fieldType,
						array(
	                        'name'      => $attribute->getAttributeCode(),
	                        'label'     => __($attribute->getFrontend()->getLabel()),
	                        'class'     => $attribute->getFrontend()->getClass(),
	                        'required'  => $attribute->getIsRequired(),
	                        'note'      => $attribute->getNote(),
	                        'disabled'  => ($product->getStoreId()>0)
							))
						->setEntityAttribute($attribute);
	
				$element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
	
				if ($inputType == 'select' || $inputType == 'multiselect') {
					$element->setValues($attribute->getSource()->getAllOptions(true, true));
				} elseif ($inputType == 'date') {
					$element->setImage($this->getSkinUrl('images/grid-cal.gif'));
					$element->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
				}
			}
		}
	}
	
	public function toJSHtml()
	{
		$html = parent::toHtml();
		// Remove line-breaks for use in JavaScript;
		$html = str_replace(array("\r\n", "\r", "\n", "\t"), '', $html);
		return $html;
	}
	
	public function getProduct()
	{
		return Mage::registry('product');
	}
	
}