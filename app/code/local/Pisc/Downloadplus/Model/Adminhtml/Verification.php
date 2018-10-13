<?php
/**
 * DownloadPlus Adminhtml Observer
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Adminhtml_Verification
{

	public function eventControllerActionPredispatch($observer)
	{
		$controller = $observer->getEvent()->getControllerAction();
		
		if ($controller instanceof Mage_Adminhtml_IndexController && $controller->getRequest()->getActionName()=='login') {
			Mage::helper('downloadplus/adminnotification')->addPrerequisiteNotification();			
		}
		
	}	

}
