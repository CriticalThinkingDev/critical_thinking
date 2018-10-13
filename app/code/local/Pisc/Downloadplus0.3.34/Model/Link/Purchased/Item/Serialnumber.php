<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloadable Link Serialnumber Item model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Link_Purchased_Item_Serialnumber extends Mage_Core_Model_Abstract
{

	protected $_eventPrefix = 'downloadplus_link_purchased_item_serialnumber';
	
	protected $_downloadDetail = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        //parent::_construct();
        $this->_init('downloadplus/link_purchased_item_serialnumber');
        $this->_setResourceModel('downloadplus/link_purchased_item_serialnumber', 'downloadplus/link_purchased_item_serialnumber_collection');
        
        // Load dependencies
        Mage::getModel('downloadable/link_purchased_item');
    }

    /*
     * Initializes specific Data
     */
    public function initialize()
    {
    	return $this;
    }

    /*
     * Returns the Hash for this Serialnumber
     */
    public function getSerialHash()
    {
    	$hash = $this->getData('serial_hash');
    	if (empty($hash)) {
    		// Dynamically create the Serial Hash
    		$linkPurchased = $this->getLinkPurchased();
    		$orderItem = $this->getOrderItem();
    		$product = $this->getProduct();

    		if ($linkPurchased->getId() && $orderItem->getId() && $product->getId()) {
    			$hash = strtr(base64_encode(microtime() . $linkPurchased->getId() . $orderItem->getId() . $product->getId()), '+/=', '-_,');
    			$this->setData('serial_hash', $hash);
    			$this->save();
    		}
    	}
    	return $hash;
    }

    /*
     * Returns the related Purchased Link entry
     */
    public function getLinkPurchased()
    {
    	$purchasedLink = Mage::getModel('downloadable/link_purchased');
    	if ($this->getOrderItemId()) {
    		$purchasedLink->load($this->getOrderItemId(), 'order_item_id');
    	}
    	return $purchasedLink;
    }

    /*
     * Returns the related Purchased Link Item entry
     */
    public function getLinkPurchasedItem()
    {
    	$purchasedItem = Mage::getModel('downloadable/link_purchased_item');
    	if ($this->getOrderItemId()) {
    		$purchasedItem->load($this->getOrderItemId(), 'order_item_id');
    	}
    	return $purchasedItem;
    }

    /*
     * Returns the related Product
     */
    public function getProduct()
    {
   		$product = Mage::getModel('catalog/product');
   		if ($id = $this->getProductId()) {
   			$product->load($id);
   		} else {
   			$orderItem = $this->getOrderItem();
   			if ($id = $orderItem->getProductId()) {
   				$product->load($id);
   			}
   		}
   		return $product;
    }

    /*
     * Returns the related Order Item
     */
    public function getOrderItem()
    {
   		$orderItem = Mage::getModel('sales/order_item');
   		if ($id = $this->getOrderItemId()) {
   			$orderItem->load($id);
   		}
   		return $orderItem;
    }

    /*
     * Returns the related Order
     */
    public function getOrder()
    {
    	$order = Mage::getModel('sales/order');
    	if ($id = $this->getOrderItem()->getOrderId()) {
    		$order->load($id);
    	}
    	return $order;
    }

    /*
     * Returns the related Customer
     */
    public function getCustomer()
    {
    	$customer = Mage::getModel('customer/customer');
    	$order = $this->getOrder();
    	if ($order->getId() && $order->getCustomerId()) {
    		$customer->load($order->getCustomerId());
    	}
    	return $customer;
    }

    /*
     * Returns the filename for this Serialnumber when used in downloads
     */
    public function getDownloadFilename()
    {
    	$config = Mage::getModel('downloadplus/config');
    	$pattern = $config->getDownloadableSerialnumbersFilenamePattern();

        $processor = Mage::getModel('core/email_template_filter');
        $variables = Array();
        $variables['customer'] = $this->getCustomer();
        $variables['order'] = $this->getOrder();
        $variables['order_item'] = $this->getOrderItem();
        $variables['product'] = $this->getProduct();

        $processor->setVariables($variables);
        try {
            $result = $processor->filter($pattern);
        }
        catch (Exception $e)   {
            $result = 'SERIALNUMBER-'.$this->getProduct()->getSku().'.txt';
        }
    	return $result;
    }

    /*
     * Sets the link status from Order
     */
    public function setStatusByOrder($order=null)
    {
    	if (is_null($order)) {
    		$order = $this->getOrder();
    	}
    	if ($order) {
	        $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING;

	        $orderItemsIds = Array();
	        $orderItemStatusToEnable = Mage::getStoreConfig(Mage_Downloadable_Model_Link_Purchased_Item::XML_PATH_ORDER_ITEM_STATUS, $order->getStoreId());

	        if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
	            $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING;
	        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED
	        			|| $order->getState() == Mage_Sales_Model_Order::STATE_CLOSED) {
	            $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED;
	        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
	            $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT;
	        } else {
	            foreach ($order->getAllItems() as $item) {
                    if ($item->getStatusId()==$orderItemStatusToEnable || $item->getStatusId()==Mage_Sales_Model_Order_Item::STATUS_SHIPPED) {
                        $orderItemsIds[] = $item->getId();
                    }
	            }
	            if ($orderItemsIds) {
	                $status = Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE;
	            }
	        }

            if ($this->getStatus() != Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
            	$this->setStatus($status);
            }
    	}

    	return $this;
    }

    /*
     * Loads the Data of the Model and the related Models
     */
    public function load($id, $field=null)
    {
    	parent::load($id, $field);
    	return $this;
    }

    /*
     * Saves the Data of the Model and the related Models
     */
    public function save()
    {
    	if ($this->getOrderItemId()) {
    		$this->setStatusByOrder();
			$this->setPurchasedId($this->getLinkPurchased()->getId());
			$this->setProductId($this->getProduct()->getId());
			$this->setLinkId($this->getLinkPurchasedItem()->getLinkId());
			$orderItem = $this->getOrderItem();
            $this->setCreatedAt($orderItem->getCreatedAt());
            $this->setUpdatedAt($orderItem->getUpdatedAt());
    	}
    	if ($this->getCreatedAt()) {
    		$this->setUpdatedAt(date('Y-m-d H:i:s'));
    	}

    	parent::save();

    	return $this;
    }

    /*
     * Deletes the Data of this Model and the related Models
     */
    public function delete()
    {
    	parent::delete();
       	return $this;
    }

    /*
     * Notifies Customer about this Download
     */
    public function notifyCustomer()
    {
    	$customer = $this->getCustomer();
		$order = $this->getOrder();
		$success = false;
		$config = Mage::getModel('downloadplus/config');

		if ($customer->getEmail()) {
			$mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->setDesignConfig(Array('area' => 'frontend'))
				->sendTransactional(
					$config->getDownloadableSerialnumbersEmailTemplate(),
					$config->getDownloadableDeliveryEmailIdentity(),
					$customer->getEmail(),
					$customer->getName(),
					Array(
							'customer'		=> $customer,
	                        'order'         => $order,
							'orderitem'		=> $this->getOrderItem(),
							'product'		=> $this->getProduct(),
	                        'billing'       => $order->getBillingAddress(),
							'linkpurchased' => $this->getLinkPurchased(),
							'linkpurchaseditem' => $this->getLinkPurchasedItem(),
	                        'serialnumber'	=> $this
					)
				);

			$success = $mailTemplate->getSentSuccess();
		}

    	return $success;
    }

    /*
     * Notifies Administrator about low count of available serialnumbers
     */
    public function notifyAdministrator($serial=null)
    {
    	$customer = $this->getCustomer();
		$order = $this->getOrder();
		$success = false;
		$config = Mage::getModel('downloadplus/config');

		$mailTemplate = Mage::getModel('core/email_template');
		$mailTemplate->setDesignConfig(Array('area' => 'frontend'))
			->sendTransactional(
				$config->getDownloadableSerialnumbersNotificationEmailTemplate(),
				$config->getDownloadableSerialnumbersNotificationEmailIdentity(),
				$config->getDownloadableSerialnumbersNotificationEmailSendTo(),
				null,
				Array(
						'customer'		=> $customer,
                        'order'         => $order,
						'orderitem'		=> $this->getOrderItem(),
						'product'		=> $this->getProduct(),
                        'billing'       => $order->getBillingAddress(),
						'serialnumber'	=> $serial
				)
			);

		$success = $mailTemplate->getSentSuccess();

		// Add store notification
		$helper = Mage::helper('downloadplus/adminnotification');
		if ($this->getOrderItem()) {
			$product = Mage::getModel('catalog/product')->load($this->getOrderItem()->getProductId());
			$helper->addNotification(
				'downloadplus-product-serialnumbers-required',
				null,
				$product->getName(),
				Mage::getUrl('adminhtml/catalog_product/edit', Array('id'=>$product->getId()))
			);
		}
		
    	return $success;
    }

    /*
     * Notifies Administrator about order with missing Serialnumber
     */
    public function notifyPending()
    {
    	$customer = $this->getCustomer();
		$order = $this->getOrder();
		$success = false;
		$config = Mage::getModel('downloadplus/config');

		$mailTemplate = Mage::getModel('core/email_template');
		$mailTemplate->setDesignConfig(Array('area' => 'frontend'))
			->sendTransactional(
				$config->getDownloadableSerialnumbersPendingEmailTemplate(),
				$config->getDownloadableSerialnumbersNotificationEmailIdentity(),
				$config->getDownloadableSerialnumbersPendingEmailSendTo(),
				null,
				Array(
					'customer'		=> $customer,
					'order'         => $order,
					'orderitem'		=> $this->getOrderItem(),
					'billing'       => $order->getBillingAddress(),
					'linkpurchased' => $this->getLinkPurchased(),
					'linkpurchaseditem' => $this->getLinkPurchasedItem()
				)
			);

		$success = $mailTemplate->getSentSuccess();

		// Add store notification
		$helper = Mage::helper('downloadplus/adminnotification');
		if ($this->getOrderItem()) {
			$product = Mage::getModel('catalog/product')->load($this->getOrderItem()->getProductId());
			$helper->addNotification(
				'downloadplus-product-serialnumbers-required',
				null,
				$product->getName(),
				Mage::getUrl('adminhtml/catalog_product/edit', Array('id'=>$product->getId()))
			);
		}
		if ($order) {
			$helper->addNotification(
				'downloadplus-order-serialnumber-required',
				null,
				$order->getIncrementId(),
				Mage::getUrl('adminhtml/sales_order/view', Array('order_id'=>$order->getId()))
			);
		}
		
    	return $success;
    }

}
