<?php
/**
 * Adminhtml Custom Renderer for Link Title
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting
 * @license	   Commercial Unlimited License
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Link_Title extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $row)
	{
		$html = $row->getLinkTitle().Mage::helper('downloadplus')->getLinkAttributesHtml($row);
		return $html;
	}

}
