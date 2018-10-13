<?php
/**
 * @category   Pisc
 * @package    Pisc_DownloadPlus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * DownloadPlus Event Observer
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.5
 */

class Pisc_Downloadplus_Model_Observer
{

	/*
	 * Used to prevent circular events within this observer
	 */
	protected $_event = null;

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

	protected function getValue(&$value, $default)
	{
		if (isset($value)) {
			return $value;
		}
		return $default;
	}

	/*
	 * Event before processing download
	 */
	public function eventDownloadProcessBefore($observer)
	{
		$download = $observer->getEvent()->getDownload();
		/*
		 $session = $download->getSession();
		 $resource = $download->getResource();
		 $resourceType = $download->getResourceType();
		 $resourceObject = $download->getResourceObject();

		 $download->setData('override_core_download', true);
		 $download->setData('redirect_url', 'home');
		 */
	}

    /**
     * Set status of link
     */
    public function eventOrderSaveAfter($observer)
    {
    	Mage::getModel('downloadable/product_type');
    	Mage::getModel('downloadplus/config');
    	
        $order = $observer->getEvent()->getOrder();
/*
        Mage::log('>>> Observer: Order Save After:', null, 'downloadplus.log');
        Mage::log('>>> Order Data:', null, 'downloadplus.log');
        Mage::log($order->getData(), null, 'downloadplus.log');
  */      
        $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING;
        $orderItemsIds = array();

        if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
            $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING;
        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED
        || $order->getState() == Mage_Sales_Model_Order::STATE_CLOSED) {
            $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED;
        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT;
        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE) {
            $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE;
        }
        
        foreach ($order->getAllItems() as $item) {
        	/*
        	Mage::log('>>> Order Item Data:', null, 'downloadplus.log');
        	Mage::log($item->getData(), null, 'downloadplus.log');
        	*/
            	
        	if ($item->getProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
        			|| $item->getRealProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)
            {
            	// Downloadable Product
            	$orderItemStatusToEnable = Mage::getStoreConfig(Mage_Downloadable_Model_Link_Purchased_Item::XML_PATH_ORDER_ITEM_STATUS, $order->getStoreId());
            	if ($item->getStatusId() == $orderItemStatusToEnable) {
            		$orderItemsIds[] = $item->getId();
            		// Trigger Event to create Serial Numbers from code for Downloadable Products
            		Mage::dispatchEvent('downloadplus_order_save_after_downloadable_create_serialnumber', Array('order'=>$order, 'order_item'=>$item));
                }
            } else {
             	// All other Products
             	$orderItemStatusToEnable = Mage::getStoreConfig(Pisc_Downloadplus_Model_Config::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_ORDERITEMSTATUS_PRODUCTS, $order->getStoreId());
             	if ($item->getStatusId() == $orderItemStatusToEnable) {
             		$orderItemsIds[] = $item->getId();
             		// Trigger Event to create Serial Numbers from code for Products
             		Mage::dispatchEvent('downloadplus_order_save_after_product_create_serialnumber', Array('order'=>$order, 'order_item'=>$item));
                }
            }
        }
        
        if (!$orderItemsIds && $status) {
            foreach ($order->getAllItems() as $item) {
                $orderItemsIds[] = $item->getId();
            }
        }

        // Update status on related Customer Link Items
        if ($orderItemsIds) {
            $links = Mage::getResourceModel('downloadplus/link_customer_item_collection')
            			->addFieldToFilter('order_item_id', array('in'=>$orderItemsIds));
            foreach ($links as $link) {
                if ($link->getStatus()!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
                	$oldStatus = $link->getStatus();
                    $link->setStatus($status)->save();
                    // Notify Customer if Link is now available
                    if ($oldStatus!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE && $link->getStatus()==Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE) {
	                	$link->notifyCustomer();
                    }
                }
            }
        }

        // Update status on related Purchased Link Serialnumbers
        if ($orderItemsIds) {
            $links = Mage::getResourceModel('downloadplus/link_purchased_item_serialnumber_collection')
            			->addFieldToFilter('order_item_id', array('in'=>$orderItemsIds));
            foreach ($links as $link) {
                if ($link->getStatus()!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
                	$oldStatus = $link->getStatus();
                    $link->setStatus($status)->save();
                    // Notify Customer if Link is now available
                    if ($oldStatus!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE && $link->getStatus()==Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE) {
	                	$link->notifyCustomer();
                    }
                }
            }
        }
        
        // Create date expiration
        if ($orderItemsIds) {
            $links = Mage::getResourceModel('downloadplus/link_purchased_item_collection')
            			->addFieldToFilter('order_item_id', array('in'=>$orderItemsIds));
            foreach ($links as $link) {
            	$expiration = Mage::getModel('downloadplus/link_purchased_item_extension');
            	if ($link->getStatus()==Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE) {
            		$expiration->createExpirationOnOrder($link);
            	}
        	}
        }

        return $this;
    }

    public function eventCoreModelSaveAfter($observer)
    {
    	$object = $observer->getEvent()->getObject();
    	
    	if ($object instanceof Mage_Downloadable_Model_Link_Purchased_Item) {

    		// Update status on related Customer Link Items
    		$links = Mage::getResourceModel('downloadplus/link_customer_item_collection')
    					->addFieldToFilter('order_item_id', array('eq'=>$object->getOrderItemId()));
    		foreach ($links as $link) {
    			if ($link->getStatus()!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
    				$oldStatus = $link->getStatus();
    				$link->setStatus($object->getStatus())->save();
    				// Notify Customer if Link is now available
    				if ($oldStatus!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE && $link->getStatus()==Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE) {
    					$link->notifyCustomer();
    				}
    			}
    		}
    		
    		// Update status on related Purchased Link Serialnumbers
    		$links = Mage::getResourceModel('downloadplus/link_purchased_item_serialnumber_collection')
    					->addFieldToFilter('order_item_id', array('eq'=>$object->getOrderItemId()));
    		foreach ($links as $link) {
    			if ($link->getStatus()!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
    				$oldStatus = $link->getStatus();
    				$link->setStatus($object->getStatus())->save();
    				// Notify Customer if Link is now available
    				if ($oldStatus!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE && $link->getStatus()==Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE) {
    					$link->notifyCustomer();
    				}
    			}
    		}
    		
    		// Create expiration date
    		$links = Mage::getResourceModel('downloadplus/link_purchased_item_collection')
    					->addFieldToFilter('order_item_id', array('eq'=>$object->getOrderItemId()));
    		foreach ($links as $link) {
    			$expiration = Mage::getModel('downloadplus/link_purchased_item_extension');
    			if ($link->getStatus()==Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE) {
    				$expiration->createExpirationOnOrder($link);
    			}
    		}
    		
    	}
    	
    	return $this;
    }
    
}
