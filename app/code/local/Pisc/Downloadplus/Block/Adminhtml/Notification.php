<?php
/**
 * DownloadplusBuilder Config Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template
{
	
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('downloadplus/notification/toolbar.phtml');
	}
	
	public function getMessage()
	{
		$message = '';
		if (!Mage::helper('downloadplus/magento')->isCompatible()) {
			$message = Mage::helper('downloadplus')->__('This DownloadPlus License is not maintained for this Magento version, functional limitations will occur. <a href="%s">Please purchase a new license for your current Magento Version at our License Store.</a>', 'https://technology.pillwax.com/software/downloadplus-for-magento.html');
		}
		return $message;
	}	
	
}
