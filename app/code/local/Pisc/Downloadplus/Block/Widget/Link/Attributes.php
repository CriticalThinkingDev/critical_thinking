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
 * @version		0.1.3
 */

class Pisc_Downloadplus_Block_Widget_Link_Attributes extends Mage_Core_Block_Template
{

	protected $_product = null;
	protected $_helper = null;

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('downloadplus/widget/link/attributes.phtml');
		$this->_helper = Mage::helper('downloadplus');
	}

	protected function _toHtml()
	{
		return parent::_toHtml();
	}

	public function getAttributes()
	{
		$attributes = Array();
		if ($link = $this->getLink()) {
			$attributes = $this->_helper->getLinkAttributes($link);
		}
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
		$data = null;
		if ($link = $this->getLink()) {
			$data = $this->_helper->getLinkAttributesData($link);
		}
		return $data;
	}
		
}
