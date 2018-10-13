<?php
/**
 * Adminhtml Custom Renderer for Expiration Field
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting
 * @license	   Commercial Unlimited License
 * @version    0.1.1
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Expires extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		$html = '';
		if (!is_null($value)) {
			$html.= Mage::helper('core')->formatDate($value, 'short', false);
			$html.= ' ('.Mage::getModel('downloadplus/link_purchased_item_extension')->getDaysUntilExpiration($value).'d) ';
			if ($this->getColumn()->hasData('type')) {
				if ($this->getColumn()->getData('type')=='form') {
					$html.= '<input type="text" class="input-text ' . $this->getColumn()->getValidateClass() . '" name="downloadable[purchasedlink]['.$row->getRelatesTo().'|'.$row->getItemId().']['.( $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId() ).']" value="" />';
				}
			}
		}
		return $html;
	}
 	
}
