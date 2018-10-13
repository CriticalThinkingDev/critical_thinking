<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Customer Download model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.4
 */

class Pisc_Downloadplus_Model_Customer_Download extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_customer_download';

	protected $_customer = null;
	protected $_purchased = null;
	protected $_items = null;

    /**
     * Constructor
     *
     */
    public function _construct()
    {
        parent::_construct();
    }

    public function setCustomer($customer)
    {
    	if ($this->_customer && $customer->getId()!=$this->_customer->getId()) {
    		$this->_items = null;
    	} 
    	$this->_customer = $customer;
    	return $this;
    }

    /**
     * Retrieve base temporary path
     *
     * @return string
     */
    public static function getBaseTmpPath($customer=null)
    {
    	$result = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'customer';
    	if ($customer && $customer->getId()) {
    		$result.= DS . $customer->getId();
    	}
    	// Create the path if it not already exists
    	if (!file_exists($result)) {
    		@mkdir($result, 0770, true);
    	}
        return $result;
    }

    /**
     * Retrieve Base files path
     *
     * @return string
     */
    public static function getBasePath($customer=null)
    {
    	$result = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'customer' . DS . 'links';
    	if ($customer && $customer->getId()) {
    		$result.= DS . $customer->getId();
    	}
    	// Create the path if it not already exists
    	if (!file_exists($result)) {
    		@mkdir($result, 0770, true);
    	}
    	return $result;
    }

    public function getLinkPurchasedCollection()
    {
    	$customerId = 0;
    	if (!is_null($this->_customer)) {
    		$customerId = $this->_customer->getId();
    	}
    	if (is_null($this->_purchased)) {
	        $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
	            ->addFieldToFilter('customer_id', $customerId)
	            ->addOrder('created_at', 'desc');

	    	$this->_purchased = $purchased;
    	}
    	return $this->_purchased;
    }

    /*
     * Returns a collection of purchased downloadable links and their logged usage
     */
    public function getLinkPurchasedItemCollection($all=false)
    {
    	if (is_null($this->_items)) {
    		$this->_items = Mage::getModel('downloadplus/data_collection');
    		
    		// Regular Order Items
    		$orderItems = Mage::getResourceModel('sales/order_item_collection');
    		$orderItems->getSelect()
    				->join(Array('order'=>$orderItems->getTable('sales/order')), 'order.entity_id=main_table.order_id',	Array('customer_id'=>'customer_id'))
    				->where('customer_id = ?', $this->_customer->getId());
    		$orderItemIds = $orderItems->getAllIds();

    		$customerId = 0;
    		if (!is_null($this->_customer)) {
    			$customerId = $this->_customer->getId();
    		}
    		
    		// Links unlocked by Serialnumber
    		$purchasedIds = Mage::getResourceModel('downloadable/link_purchased_collection')
    							->addFieldToFilter('order_id', Array('eq'=>0))
    							->addFieldToFilter('customer_id', Array('eq'=>$customerId))
    							->getAllIds();
    		
	        if (count($orderItemIds) || count($purchasedIds)) {
	        	$collection = Mage::getResourceModel('downloadplus/link_purchased_item_collection');
	        
	        	$sql = "(";
	        	$sql.= "(SELECT UUID() AS id, '".Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_PURCHASED."' AS relates_to,
						item_id,
		        		NULL as order_id,
	  					purchased_id,
						order_item_id,
						product_id,
		        		NULL as customer_id,
						link_hash,
						number_of_downloads_bought,
						number_of_downloads_used,
						link_id,
						link_title,
						is_shareable,
						link_url,
						link_file,
						link_type,
						status,
						created_at,
						updated_at,
		        		NULL as attributes
	        		FROM ".$collection->getTable('downloadable/link_purchased_item')." AS link_purchased_item)";
	        	if (Mage::helper('downloadplus')->existsDownloadplusBonus()) {
	        		$sql.= " UNION ALL ";
	        		$sql.= "(SELECT UUID() as id, '".Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_BONUS."' AS relates_to,
							item_id,
			        		order_id,
		  					'0' as purchased_id,
							order_item_id,
							product_id,
			        		customer_id,
							link_hash,
							number_of_downloads_bought,
							number_of_downloads_used,
							link_id,
							link_title,
							is_shareable,
							link_url,
							link_file,
							link_type,
							status,
							created_at,
							updated_at,
			        		attributes
		        		FROM ".$collection->getTable('downloadplusbonus/link_purchased_bonus_item')." AS link_bonus_item)";
	        	}
	        	$sql.= ")";
	        	 
	        	$allItems = new Zend_DB_Expr($sql);
	        
	        	$collection->getSelect()
			        	->reset()
			        	->from(Array('main_table'=>$allItems))
			        	->where('main_table.order_item_id IN (?)', $orderItemIds)
	        			->orWhere('main_table.purchased_id IN (?)', $purchasedIds);
	        
	        	/* Exclude Magazine Addon Links from this Collection */
	        	$collection->addPurchasedLinksToResult()
			        ->addFieldToFilter('main_table.status', array('nin' => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT))
			        ->addFieldToFilter('main_table.link_type', array('nin' => explode(',', Pisc_Downloadplus_Helper_Download::FRONTEND_FILTER_LINK_TYPES)));
	        
	        	$collection->getSelect()
		        	->order(Array('relates_to ASC', 'item_id DESC'));

	        	foreach ($collection as $item) {
	        		$item->setData('newest_download_timestamp', null);
	        		 
	        		// Add timestamp of last download to each item
	        		$data = Mage::getModel('downloadplus/log')
				        		->getResource()
				        		->clearFilter()
				        		->addPurchasedLinkToFilter($item)
				        		->getNewestDownloadTimestamp();
	        		
	        		if ($data) {
	        			$item->setData('newest_download_timestamp', date('Y-m-d H:i:s', strtotime($data)));
	        		} else {
	        			$data = Mage::getModel('downloadplus/log')
				        			->getResource()
				        			->clearFilter()
				        			->addPurchasedBonusLinkToFilter($item)
				        			->getNewestDownloadTimestamp();
						if ($data) {	        			
	        				$item->setData('newest_download_timestamp', date('Y-m-d H:i:s', strtotime($data)));
						}
	        		}

	        		$item->setData('expires_on', null);
	        		
	        		$object = Mage::getModel('catalog/product')->load($item->getProductId());
	        		$item->setData('product_sku', $object->getSku());
	        		$item->setData('product_name', $object->getName());

	        		if ($item->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_PURCHASED) {
	        			$object = Mage::getModel('downloadable/link_purchased')->load($item->getPurchasedId());
	        			$item->setData('order_increment_id', $object->getOrderIncrementId());
	        		
	        			$object = Mage::getModel('downloadplus/link_purchased_item_extension')->loadByPurchasedLink($item);
	        			if ($object->getId()) {
	        				$item->setData('expires_on', $object->getExpiresOn());
	        			}
	        		}
	        		if ($item->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_BONUS) {
	        			$object = Mage::getModel('sales/order')->load($item->getOrderId());
	        			$item->setData('order_increment_id', $object->getIncrementId());
	        		}
	        		
	        		$this->_items->addItem($item);
	        	}
	        }
    	}

    	return $this->_items;
    }

    /*
     * Sends a Transactional Email
     */
    public function notifyCustomer($download)
    {
    	$customer = $download->getCustomer();
		$order = $download->getOrder();
		$success = false;
		$config = Mage::getModel('downloadplus/config');

		if ($customer->getEmail()) {
			$mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->setDesignConfig(Array('area' => 'frontend'))
				->sendTransactional(
					$config->getDownloadableDeliveryEmailTemplate(),
					$config->getDownloadableDeliveryEmailIdentity(),
					$customer->getEmail(),
					$customer->getName(),
					Array(
							'customer'		=> $customer,
	                        'order'         => $order,
							'orderitem'		=> $download->getOrderItem(),
	                        'billing'       => $order->getBillingAddress(),
							'linkpurchased' => $download->getLinkPurchased(),
							'linkpurchaseditem' => $download->getLinkPurchasedItem(),
	                        'download'		=> $download
					)
				);

			$success = $mailTemplate->getSentSuccess();
		}

    	return $success;
    }

}