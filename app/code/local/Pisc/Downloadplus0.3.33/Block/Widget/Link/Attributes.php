<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2011 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Widget Link Attributes block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

class Pisc_Downloadplus_Block_Widget_Link_Attributes extends Mage_Core_Block_Template
{

	protected $_product = null;

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('downloadplus/widget/link/attributes.phtml');
	}

	protected function _toHtml()
	{
		return parent::_toHtml();
	}

	public function getAttributes()
	{
		$attributes = Mage::getModel('downloadplus/link')->load($this->getLink()->getLinkId())->getAttributes();
		return $attributes;
	}
	
	/**
	* $excludeAttr is optional array of attribute codes to
	* exclude them from additional data array
	*
	* @param array $excludeAttr
	* @return array
	*/
	public function getAdditionalData(array $excludeAttr = array())
	{
		$data = array();
		$attributes = $this->getAttributes();
		foreach ($attributes as $attribute) {
			//if ($attribute->getIsVisibleOnFront() && $attribute->getIsUserDefined() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
			if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
				$object = new Varien_Object();
				$object->setData($attribute->getAttributeCode(), $attribute->getValue());
				$value = $attribute->getFrontend()->getValue($object);
	
				if (is_string($value) && strlen($value)) {
					if ($attribute->getFrontendInput() == 'price') {
						$value = Mage::app()->getStore()->convertPrice($value,true);
					} elseif (!$attribute->getIsHtmlAllowedOnFront()) {
						$value = $this->htmlEscape($value);
					}
					$data[$attribute->getAttributeCode()] = array(
                           'label' => $attribute->getFrontend()->getLabel(),
                           'value' => $value,
                           'code'  => $attribute->getAttributeCode()
					);
				}
			}
		}
		return $data;
	}
		
}
