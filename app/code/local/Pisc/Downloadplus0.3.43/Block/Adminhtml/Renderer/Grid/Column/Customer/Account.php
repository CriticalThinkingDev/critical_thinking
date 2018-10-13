<?php
/**
* Grid Column renderer with Link to Customer Account
*
* @author     PILLWAX Industrial Solutions Consulting
* @category   Pisc
* @package    Pisc_Downloadplus
* @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
* @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
* @version    0.1.0
*/

class Pisc_Downloadplus_Block_Adminhtml_Renderer_Grid_Column_Customer_Account extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	/**
	* Renders grid column
	*
	* @param Varien_Object $row
	* @return mixed
	*/
	public function _getValue(Varien_Object $row)
	{
		$html = '<a href="'.Mage::helper("adminhtml")->getUrl('administrator/customer/edit', Array('id'=>$row->getCustomerId())).'">'.parent::_getValue($row).'</a>';
		
		return $html;
	}
	
}
