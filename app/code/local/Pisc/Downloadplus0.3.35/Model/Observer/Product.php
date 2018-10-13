<?php
/**
 * @category   Pisc
 * @package    Pisc_DownloadPlus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * DownloadPlus Product Event Observer
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Observer_Product
{

	/*
	 * Returns true if in Admin Session
	 */
	public function isAdminSession()
	{
		$session = Mage::getSingleton('admin/session');
		if ($session) {
			return $session->isLoggedIn();
		}
		return false;
	}

	/*
	 * Returns Session model
	 */
	protected function getSession()
	{
		return Mage::getSingleton('core/session');
	}

	/*
	 * Assigns a Serialnumber to a Orderitem
	 */
	public function assignSerialnumber($orderItem, $serial=null)
	{
		$config = Mage::getModel('downloadplus/config');
		if (is_null($serial)) {
			$serial = Mage::getModel('downloadplus/link_purchased_item_serialnumber');
		}

		$_serial = $serial->getSerialNumber();
		if (empty($_serial)) {
			// Set the Serialnumber from Product
			$serialNumber = Mage::getModel('downloadplus/product_serialnumber')->loadNextByProduct($orderItem->getProductId());

			if ($orderItem->getParentItemId()) {
				$serial->setOrderItemId($orderItem->getParentItemId());
			} else {
				$serial->setOrderItemId($orderItem->getId());
			}
			$serial->setSerialTitle(Mage::helper('downloadplus')->__('Serialnumber for ').$orderItem->getName());

			if ($serialNumber && $serialNumber->getSerialNumber()) {
				$serial->setSerialNumber($serialNumber->getSerialNumber());
				$serial->setSerialHash($serialNumber->getSerialHash());

				// Save the Serialnumber, its Status is being updated with the Order updates
				$serial->save();

				// Delete original Serialnumber from Product
				$serialNumber->delete();

				// Notify Customer
				if ($config->isDownloadableSerialnumbersSendEmailFrontend() || $config->isDownloadableSerialnumbersSendEmailAdmin()) {
					$serial->notifyCustomer();
				}
				
				// Notify Admin if available serialnumbers get low
				if ($config->getDownloadableSerialnumbersNotificationCount()) {
					$object = new Varien_Object();
					$object->setProductId($orderItem->getProductId());
					$object->setAvailableCount($serialNumber->getCountByProduct($orderItem->getProductId()));
					$object->setNotificationLimit($config->getDownloadableSerialnumbersNotificationCount());

					if ($object->getAvailableCount()<=$config->getDownloadableSerialnumbersNotificationCount()) {
						$serial->notifyAdministrator($object);
					}
				}
				
				/*
				Mage::log('>>> Serialnumber generated:', null, 'downloadplus.log');
				Mage::log($serial, null, 'downloadplus.log');
				*/
			} else {
				// Notify Admin to manually add Serialnumber
				$serial->notifyPending();
				
				/*
				Mage::log('>>> Serialnumber required.', null, 'downloadplus.log');
				*/
			}
		}
	}

	/*
	 * Assigns Product Serialnumber when purchased
	 */
	public function eventProductAssignSerialnumber($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$orderItem = $observer->getEvent()->getOrderItem();

		$helper = Mage::app()->getHelper('downloadplus');

		/*
		Mage::log('>>> Observer: Product Serialnumber:', null, 'downloadplus.log');
		Mage::log('>>> Order Data:', null, 'downloadplus.log');
		Mage::log($order->getData(), null, 'downloadplus.log');
		Mage::log('>>> Order Item Data:', null, 'downloadplus.log');
		Mage::log($orderItem->getData(), null, 'downloadplus.log');
		*/
		
		if ($orderItem->getId() && $helper->hasSerialnumbers($orderItem->getProductId())) {

			$serials = Mage::getModel('downloadplus/link_purchased_item_serialnumber')
					->getCollection()
					->getByOrderItemId($orderItem->getId());

			$qty = 0;
			// Check already assigned serialnumbers
			foreach ($serials as $serial) {
				$serialNumber = $serial->getSerialNumber();
				if (empty($serialNumber)) {
					$this->assignSerialnumber($orderItem, $serial);
				}
				$qty++;
			}
			// Assign additional serialnumbers
			while ($qty<$orderItem->getQtyOrdered()) {
				$this->assignSerialnumber($orderItem);
				$qty++;
			}

		}
	}

}
