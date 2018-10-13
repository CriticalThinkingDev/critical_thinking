<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloadable Link Customer Item model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.2
 */

class Pisc_Downloadplus_Model_Link_Customer_Item extends Mage_Downloadable_Model_Link_Purchased_Item
{
	
	protected $_eventPrefix = 'downloadplus_link_customer_item';

	protected $_downloadDetail = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        //parent::_construct();
        $this->_init('downloadplus/link_customer_item');
        $this->_setResourceModel('downloadplus/link_customer_item', 'downloadplus/link_customer_item_collection');
    }

    /*
     * Initializes specific Data
     */
    public function initialize()
    {
    	return $this;
    }

    /*
     * Returns a Id by Filename
     */
    public function getIdByFile($file)
    {
    	$result = null;

    	if (!empty($file)) {
	        $sql = $this->_getResource()->getReadConnection()->select()
		            	->from($this->_getResource()->getTable('downloadplus/link_customer_item'))
		            	->where("link_file='".$file."'");

	        if ($result = $this->_getResource()->getReadConnection()->fetchOne($sql)) {
	        	return $result;
	        }
    	}

        return null;
    }

    /*
     * Returns the related Download Detail
     */
    public function getDownloadDetail()
    {
    	if (is_null($this->_downloadDetail)) {
    		$this->_downloadDetail = Mage::getModel('downloadplus/download_detail');
	    	if ($id = $this->getId()) {
	    		$this->_downloadDetail->loadByLinkCustomerItemId($id);
	    	} else {
	    		$this->_downloadDetail->create($this->getLinkFile(), Mage::getModel('downloadplus/customer_download')->getBasePath());
	    	}
    	}
    	return $this->_downloadDetail;
    }

    /*
     * Returns the Download Description
     */
    public function getDescription()
    {
    	$result = $this->getDownloadDetail()->getDetail();
    	return $result;
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
   		$orderItem = $this->getOrderItem();
   		if ($id = $orderItem->getProductId()) {
   			$product->load($id);
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
     * Creates a download hash
     */
    public function createLinkHash()
    {
    	if (!$this->getLinkHash() && $this->getId() && $this->getOrderItemId() && $this->getProductId()) {
    		$this->setLinkHash(strtr(base64_encode(microtime() . $this->getId() . $this->getOrderItemId() . $this->getProductId()), '+/=', '-_,'));
    	}
    	return $this;
    }

    /*
     * Moves the uploaded Link File to its location and sets link file data
     */
    public function updateLinkFile($source, $destination, $customer)
    {
    	$sourceFile = Mage::getModel('downloadplus/customer_download')->getBaseTmpPath($customer).DS.$source;
    	$destFile = Mage::getModel('downloadplus/customer_download')->getBasePath($customer).DS.$destination;

    	if (file_exists($sourceFile)) {
    		if (file_exists($destFile)) {
    			@unlink($destFile);
    		}
    		@rename($sourceFile, $destFile);
    		if (file_exists($destFile)) {
    			@chmod($destFile, 0770);
    			$this->setLinkFile('/'.$customer->getId().'/'.$destination);
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

    	$this->getDownloadDetail();
    	return $this;
    }

    /*
     * Saves the Data of the Model and the related Models
     */
    public function save()
    {
    	if ($this->getOrderItemId()) {
    		$this->setStatusByOrder();
    		$this->setCustomerId($this->getCustomer()->getId());
			$this->setPurchasedId($this->getLinkPurchased()->getId());
			$this->setProductId($this->getProduct()->getId());
			$this->setLinkId($this->getLinkPurchasedItem()->getLinkId());
			$orderItem = $this->getOrderItem();
            $this->setCreatedAt($orderItem->getCreatedAt());
            $this->setUpdatedAt($orderItem->getUpdatedAt());
    	}

    	parent::save();

    	/*
    	 * Create a Link Hash if all data is available
    	 */
    	if (!$this->getLinkHash()) {
    		$this->createLinkHash();
    		parent::save();
    	}

    	$this->getDownloadDetail()->makeActive();
    	$this->getDownloadDetail()->setProductId($this->getProductId());
    	$this->getDownloadDetail()->setLinkCustomerItemId($this->getId());
    	$this->getDownloadDetail()->save();

    	return $this;
    }

    /*
     * Deletes the Data of this Model and the related Models
     */
    public function delete()
    {
    	if ($this->getDownloadDetail()->getId()) {
    		$this->getDownloadDetail()->delete();
    	}
    	parent::delete();

       	return $this;
    }

    /*
     * Returns the Download URL to this Link
     */
    public function getUrl()
    {
		$config = Mage::getModel('downloadplus/config');
		$params = array('id' => $this->getLinkHash());

		if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

    	$result = Mage::getUrl('downloadable/download/customer', $params);
    	return $result;
    }

    /*
     * Notifies Customer about this Download
     */
    public function notifyCustomer()
    {
    	return Mage::getModel('downloadplus/customer_download')->notifyCustomer($this);
    }
}
