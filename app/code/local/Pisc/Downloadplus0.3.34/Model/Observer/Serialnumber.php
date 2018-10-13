<?php
/**
 * @category   Pisc
 * @package    Pisc_DownloadPlus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * DownloadPlus Serialnumber Event Observer
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.4
 */

class Pisc_Downloadplus_Model_Observer_Serialnumber
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
	 * EXAMPLE OBSERVER:
	 * Event when processing orders for creating serial numbers
	 */
	public function eventDownloadableCreateSerialnumber($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$orderItem = $observer->getEvent()->getOrderItem();

		// Check if this order number has a serial number already
		if ($orderItem->getId()) {
			$serial = Mage::getModel('downloadplus/link_purchased_item_serialnumber');
			$serial->load($orderItem->getId(), 'order_item_id');
			if (is_null($serial->getId())) {
				$serial->setOrderItemId($orderItem->getId());
				$serial->setSerialTitle(Mage::helper('downloadplus')->__('Serialnumber for ').$orderItem->getName());

				// Set the Serialnumber here
				$serial->setSerialNumber('');

				// Save the Serialnumber, its Status is being updated with the Order updates
				$serial->save();
			}
		}
	}

	/*
	 * Assigns a Serialnumber to a Orderitem
	 */
	public function assignSerialnumber($orderItem, $purchasedLink, $serial=null)
	{
		$config = Mage::getModel('downloadplus/config');
		if (is_null($serial)) {
			$serial = Mage::getModel('downloadplus/link_purchased_item_serialnumber');
		}
	
		$_serial = $serial->getSerialNumber();
		if (empty($_serial)) {
			// Set the Serialnumber from Product
			$serialNumber = Mage::getModel('downloadplus/product_serialnumber')->loadNextByLink($purchasedLink->getLinkId());
	
			if ($orderItem->getParentItemId()) {
				$serial->setOrderItemId($orderItem->getParentItemId());
			} else {
				$serial->setOrderItemId($orderItem->getId());
			}
			$serial->setSerialTitle(Mage::helper('downloadplus')->__('Serialnumber for %s (%s)', $orderItem->getName(), $purchasedLink->getLinkTitle()));
	
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
				$count = $serialNumber->getCountByLink($purchasedLink->getLinkId());
				if ($count<=$config->getDownloadableSerialnumbersNotificationCount()) {
					$object = new Varien_Object();
					$object->setProductId($orderItem->getProductId());
					$object->setPurchasedLink($purchasedLink);
					$object->setAvailableCount($count);
					$object->setNotificationLimit($config->getDownloadableSerialnumbersNotificationCount());
	
					$serial->notifyAdministrator($object);
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

		$purchasedLink = Mage::getModel('downloadable/link_purchased_item')
							->load($orderItem->getId(), 'order_item_id');
		
		$helper = Mage::app()->getHelper('downloadplus');

		/*
		Mage::log('>>> Observer: Downloadable Link Serialnumber:', null, 'downloadplus.log');
		Mage::log('>>> Order Data:', null, 'downloadplus.log');
		Mage::log($order->getData(), null, 'downloadplus.log');
		Mage::log('>>> Order Item Data:', null, 'downloadplus.log');
		Mage::log($orderItem->getData(), null, 'downloadplus.log');
		Mage::log('>>> Purchased Link Data:', null, 'downloadplus.log');
		Mage::log($purchasedLink->getData(), null, 'downloadplus.log');
		*/
		
		if ($purchasedLink->getLinkId() && $helper->hasSerialnumbers($orderItem->getProductId(), $purchasedLink->getLinkId()) && 
			($orderItem->getProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
			|| $orderItem->getRealProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)) {

			// Assign serialnumber to quantity of the purchased link
			$qty = 0;
			$serials = Mage::getModel('downloadplus/link_purchased_item_serialnumber')
							->getCollection()
							->getByOrderItemId($orderItem->getId());
		
			// Check already assigned serialnumbers
			foreach ($serials as $serial) {
				$serialNumber = $serial->getSerialNumber();
				if (empty($serialNumber)) {
					$this->assignSerialnumber($orderItem, $purchasedLink, $serial);
				}
				$qty++;
			}
			// Assign additional serialnumbers
			while ($qty<$orderItem->getQtyOrdered()) {
				$this->assignSerialnumber($orderItem, $purchasedLink);
				$qty++;
			}

		}
	}
	
}
