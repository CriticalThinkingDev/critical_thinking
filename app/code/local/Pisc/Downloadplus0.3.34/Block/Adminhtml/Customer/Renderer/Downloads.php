<?php
/**
 * Adminhtml Custom Renderer for Downloads Field
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting
 * @license	   Commercial Unlimited License
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Downloads extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $row)
	{
		$html = null;
		if ($row->getData('number_of_downloads_bought')>0) {
			$html = $this->__('%s of %s', $row->getData('number_of_downloads_used'), $row->getData('number_of_downloads_bought'));
		} else {
			$html = $this->__('%s of unlimited', $row->getData('number_of_downloads_used'));
		}
		return $html;
	}
	
	public function renderCss()
	{
		return parent::renderCss() . ' a-right';
	}
	
}
