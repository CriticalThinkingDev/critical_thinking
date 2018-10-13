<?php
/**
 * Downloadplus Purchased Link Serialnumber Observer
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.11
 */

class Pisc_Downloadplus_Model_Observer_Serialnumber
{

    protected $_config = null;

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
                $serial->setSerialTitle(Mage::helper('downloadplus')->__('Serialnumber for ') . $orderItem->getName());

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
    public function assignSerialnumber($orderItem, $purchasedLink, $serial = null)
    {
        $config = Mage::getModel('downloadplus/config')->setStore($orderItem->getStoreId());

        // Check if purchased Link has Serialnumbers active
        if (! Mage::app()->getHelper('downloadplus')->assignSerialnumberToLink($purchasedLink->getLinkId())) {
            return false;
        }

        if (is_null($serial)) {
            $serial = Mage::getModel('downloadplus/link_purchased_item_serialnumber');

            if ($orderItem->getParentItemId()) {
                $serial->setOrderItemId($orderItem->getParentItemId());
            } else {
                $serial->setOrderItemId($orderItem->getId());
            }
            $serial->setSerialTitle(Mage::helper('downloadplus')->__('Serialnumber for %s (%s)', $orderItem->getName(), $purchasedLink->getLinkTitle()));
            $serial->setLinkPurchasedItem($purchasedLink);
        } else {
            // If Serialnumber is existing we may need to expire it
            if ($this->_config && $this->_config->isDownloadableSerialnumberExpireWithLink()
                && ($purchasedLink->getStatus() == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED
                    || $purchasedLink->getStatus() == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE)) {

                $serial->setStatus($purchasedLink->getStatus())->save();
            }
        }

        $_serial = $serial->getSerialNumber();

        // Only assign Serialnumber when needed and we are not expired here
        if (empty($_serial) && $serial->getStatus()!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
            // Set the Serialnumber from Product
            $serialNumber = Mage::getModel('downloadplus/product_serialnumber')->loadNextByLink($purchasedLink->getLinkId());

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
                if ($count <= $config->getDownloadableSerialnumbersNotificationCount()) {
                    $object = new Varien_Object();
                    $object->setProductId($orderItem->getProductId());
                    $object->setPurchasedLink($purchasedLink);
                    $object->setAvailableCount($count);
                    $object->setNotificationLimit($config->getDownloadableSerialnumbersNotificationCount());

                    $serial->notifyAdministrator($object);
                }

                /*
                 * Mage::log('>>> Serialnumber generated:', null, 'downloadplus.log');
                 * Mage::log($serial, null, 'downloadplus.log');
                 */
            } else {
                $notify = is_null($serial->getId());
                $serial->save();
                // Notify Admin to manually add Serialnumber, only for new created Purchased Link Serialnumber
                if ($notify) {
                    $serial->notifyPending();
                }

                /*
                 * Mage::log('>>> Serialnumber required.', null, 'downloadplus.log');
                 */
            }
        }
    }

    /*
     * Assigns Product Serialnumber when purchased
     */
    public function eventProductAssignSerialnumber($observer)
    {
        $helper = Mage::app()->getHelper('downloadplus');
        $order = $observer->getEvent()->getOrder();
        $orderItem = $observer->getEvent()->getOrderItem();

        $this->_config = Mage::getModel('downloadplus/config')->setStore($order->getStoreId());

        $purchasedLinks = Mage::getResourceModel('downloadplus/link_purchased_item_collection')->addFieldToFilter('order_item_id', array(
            'in' => $orderItem->getId()
        ));

        foreach ($purchasedLinks as $purchasedLink) {
            /*
             * Mage::log('>>> Observer: Downloadable Link Serialnumber:', null, 'downloadplus.log');
             * Mage::log('>>> Order Data:', null, 'downloadplus.log');
             * Mage::log($order->getData(), null, 'downloadplus.log');
             * Mage::log('>>> Order Item Data:', null, 'downloadplus.log');
             * Mage::log($orderItem->getData(), null, 'downloadplus.log');
             * Mage::log('>>> Purchased Link Data:', null, 'downloadplus.log');
             * Mage::log($purchasedLink->getData(), null, 'downloadplus.log');
             */

            if ($purchasedLink->getLinkId() && $helper->assignSerialnumberToLink($purchasedLink->getLinkId())
                && ! $helper->hasSerialnumbersDeactivated($orderItem->getProductId())
                && $helper->hasSerialnumbers($orderItem->getProductId(), $purchasedLink->getLinkId())
                && ($orderItem->getProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                    || $orderItem->getRealProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)) {

                // Assign serialnumber to quantity of the purchased link
                $qty = 0;
                $serials = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->getCollection()->getByPurchasedItemId($purchasedLink->getId());

                // Check already assigned serialnumbers
                foreach ($serials as $serial) {
                    $serialNumber = $serial->getSerialNumber();
                    if (empty($serialNumber)) {
                        $this->assignSerialnumber($orderItem, $purchasedLink, $serial);
                    }
                    $qty ++;
                }
                // Assign additional serialnumbers
                while ($qty < $orderItem->getQtyOrdered()) {
                    $this->assignSerialnumber($orderItem, $purchasedLink);
                    $qty ++;
                }
            }
        }
    }
}
