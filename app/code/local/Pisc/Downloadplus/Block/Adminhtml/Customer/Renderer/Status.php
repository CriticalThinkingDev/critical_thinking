<?php
/**
 * Adminhtml Custom Renderer for Status Field
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting
 * @license	   Commercial Unlimited License
 * @version    0.1.1
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $row)
	{
		$html = '<select name="downloadable[purchasedlink]['.$row->getRelatesTo().'|'.$row->getItemId().']['.( $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId() ).']" ' . $this->getColumn()->getValidateClass() . '">';
		$value = $row->getData($this->getColumn()->getIndex());
		foreach ($this->getColumn()->getOptions() as $val => $label){
			$selected = ( ($val == $value && (!is_null($value))) ? ' selected="selected"' : '' );
			$html.= '<option value="' . $val . '"' . $selected . '>' . $label . '</option>';
		}
		$html.='</select>';
		return $html;
	}
	
}
