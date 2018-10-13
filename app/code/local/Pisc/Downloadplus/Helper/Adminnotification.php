<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Downloadplus AdminNotification helper
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @version		0.1.2
 */

class Pisc_Downloadplus_Helper_Adminnotification extends Mage_Core_Helper_Data
{

	const SEVERITY_CRITICAL = 1;
	const SEVERITY_MAJOR    = 2;
	const SEVERITY_MINOR    = 3;
	const SEVERITY_NOTICE   = 4;
	
	/*
	 * Returns true if in Admin Session
	 */
	protected function isAdminSession()
	{
		$result = Mage::app()->getStore()->isAdmin();
		return $result;
	}

	/*
	 * Adds a Notification Message to the AdminNotification Inbox
	 */
	public function addNotification($notificationId, $exception=null, $detail=null, $url=null)
	{
		$config = Mage::getModel('downloadplus/config');
		$notify = null;

		// Add Administrator Notification
		$message = Mage::getModel('adminnotification/inbox');

		$message->setDateAdded(Mage::getModel('core/date')->date());
		$message->setTitle($this->__('DownloadPlus for Magento has detected a limitation on its functions'));
		$message->setSeverity($config->getOptionAdminnotificationsType());

		$description = '';
		$frontendMessage = '';
		switch($notificationId) {
			case 'downloadplus-prerequisite':
				$message->setSeverity(self::SEVERITY_MAJOR);
				$description.= $this->__('This DownloadPlus License is not maintained for this Magento version, functional limitations will occur. Please purchase a new license for your current Magento Version at our License Store.');
				$message->setUrl('https://technology.pillwax.com/software/downloadplus-for-magento.html');
				$notify = true;
				break;
			case 'downloadplus-file-not-found':
				$description.= $this->__('The following downloadable file is missing or inaccessible:');
				break;
			case 'downloadplus-transmission-failed':
				$description.= $this->__('Transmission of the following downloadable file has failed:');
				break;
			case 'downloadplus-product-serialnumbers-required':
				$message->setTitle($this->__('Serialnumbers required for Product'));
				$description.= $this->__('Serialnumbers required for product:').' ';
				break;
			case 'downloadplus-order-serialnumber-required':
				$message->setTitle($this->__('Serialnumber required for Order'));
				$description.= $this->__('Serialnumber required for order:').' ';
				break;
			case 'downloadplus-builder':
				$message->setTitle($this->__('Download Builder for Downloadable Products'));
				$message->setUrl('https://support.pillwax.com/open-source/doku.php?id=magento:downloadplus:builder');
				break;
			default:
				$message->setSeverity(self::SEVERITY_NOTICE);
				$message->setTitle($this->__('Downloadable Products'));
				$description.= $this->__('A general Error occured related to the functions of Downloadplus.');
				break;
		}
		if ($detail) { $description.= ' '.$detail; }
		if ($exception && isset($exception->faultstring)) {
			$description.= ' '.$this->__('The following Message was received:').' "'.$exception->faultstring.'"';
		}
		if ($exception && ($exception instanceof Zend_Soap_Client_Exception || $exception instanceof Exception)) {
			$description.= ' '.$this->__('The following Message was received:').' "'.$exception->getMessage().'"';
		}
		$message->setDescription($description);

		if ($url) {
			$message->setUrl($url);
		}
		if (!$message->getUrl()) {
			$message->setUrl('https://support.pillwax.com/open-source/doku.php?id=magento:downloadplus');
		}

		if ($config->getOptionAdministratorNotifications() || $notify===true) {
			$message->save();
		}

		return $this;
	}

	public function addPrerequisiteNotification()
	{
		$helper = Mage::helper('downloadplus/magento');
		if (!$helper->isCompatible()) {
			$this->addNotification(
					'downloadplus-prerequisite', 
					null,
					$this->__('The installed License is maintained for Magento %s, this Magento installation is detected as %s (Community Edition equivalent).', $helper->getMaintainedVersion(), $helper->getMajorVersion())
				);
		}
	}
	
}

?>