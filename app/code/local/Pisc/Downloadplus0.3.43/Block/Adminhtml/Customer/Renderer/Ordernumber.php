<?php
/**
 * Adminhtml Custom Renderer for Downloads Field
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting
 * @license	   Commercial Unlimited License
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Ordernumber extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $row)
	{
		$html = null;
		if ($row->getData('order_increment_id')>0) {
			$html = $row->getData('order_increment_id');
		} else {
			$extension = Mage::getModel('downloadplus/link_purchased_item_extension')->loadByItemId($row->getId());
			if ($extension->getId() && $extension->getUnlockSerialNumber()) {
				$html = Mage::helper('downloadplus')->__('Unlocked by Serialnumber <pre>%s</pre>', $extension->getUnlockSerialNumber());
			}
		}
		return $html;
	}
	
}
