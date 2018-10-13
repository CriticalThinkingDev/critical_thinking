<?php
/**
* Serialnumber Pool renderer
*
* @author     PILLWAX Industrial Solutions Consulting
* @category   Pisc
* @package    Pisc_Downloadplus
* @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
* @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
* @version    0.1.0
*/

class Pisc_Downloadplus_Block_Adminhtml_Renderer_Grid_Column_Serialnumber_Pool extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	/**
	* Renders grid column
	*
	* @param Varien_Object $row
	* @return mixed
	*/
	public function _getValue(Varien_Object $row)
	{
		Mage::getModel('downloadplus/config');
		$data = parent::_getValue($row);
		
		if ($data==Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT) {
			$data = Mage::helper('downloadplus')->__('- Whole Product -');
		}
		if (empty($data)) {
			$data = Mage::helper('downloadplus')->__('* Unassigned, please remove *');
		}
		return $data;
	}
	
}
