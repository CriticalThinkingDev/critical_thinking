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
 * @version		0.1.0
 */

class Pisc_Downloadplus_Helper_Adminnotification extends Mage_Core_Helper_Data
{

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
		if (!$config->getOptionAdministratorNotifications()) {
			return $this;
		}

		// Add Administrator Notification
		$message = Mage::getModel('adminnotification/inbox');

		$message->setDateAdded(gmdate('Y-m-d H:i:s'));
		$message->setTitle($this->__('Downloadplus for Magento has detected a limitation on its functions'));
		$message->setSeverity($config->getOptionAdminnotificationsType());

		$description = '';
		$frontendMessage = '';
		switch($notificationId) {
			case 'downloadplus-file-not-found':
				$description.= $this->__('The following downloadable file is missing or inaccessible:').' ';
				$description.= is_null($detail)?'':$detail.' ';
				//$message->setUrl('');
				//$frontendMessage = '';
				break;
			case 'downloadplus-transmission-failed':
				$description.= $this->__('Transmission of the following downloadable file has failed:').' ';
				$description.= is_null($detail)?'':$detail.' ';
				//$message->setUrl('');
				//$frontendMessage = '';
				break;
			case 'downloadplus-product-serialnumbers-required':
				$message->setTitle($this->__('Serialnumbers required for Product'));
				$description.= $this->__('Serialnumbers required for product:').' ';
				$description.= is_null($detail)?'':$detail.' ';
				if ($url) {
					$message->setUrl($url);
				}
				//$frontendMessage = '';
				break;
			case 'downloadplus-order-serialnumber-required':
				$message->setTitle($this->__('Serialnumber required for Order'));
				$description.= $this->__('Serialnumber required for order:').' ';
				$description.= is_null($detail)?'':$detail.' ';
				if ($url) {
					$message->setUrl($url);
				}
				//$frontendMessage = '';
				break;
			default:
				$message->setSeverity(self::SEVERITY_NOTICE);
				$description.= $this->__('A general Error occured related to the functions of Downloadplus.').' ';
				$description.= is_null($detail)?'':$detail.' ';
				$message->setUrl('https://support.pillwax.com/open-source/doku.php?id=magento:downloadplus');
				break;
		}

		if ($exception && isset($exception->faultstring)) {
			$description.= $this->__('The following Message was received:').' "'.$exception->faultstring.'"';
		}
		if ($exception && ($exception instanceof Zend_Soap_Client_Exception || $exception instanceof Exception)) {
			$description.= $this->__('The following Message was received:').' "'.$exception->getMessage().'"';
		}
		
		$message->setDescription($description);
		$message->save();

		return $this;
	}

}

?>