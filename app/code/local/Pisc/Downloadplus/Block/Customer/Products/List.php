<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Products List for Customer
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.11
 */

class Pisc_Downloadplus_Block_Customer_Products_List extends Mage_Core_Block_Template
{

	protected $_sortVersionHistory = 'version DESC';
	protected $_sortProducts = 'product_name ASC';
	protected $_sortPurchasedItems = 'item_id DESC';
	protected $_session = null;
	protected $_pager = true;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_session = Mage::getSingleton('customer/session');

        $this->updateCollection();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getItems()) {
            if ($this->_pager) {
                if (!$pager = $this->getLayout()->getBlock('downloadplus.customer.products.pager')) {
                    $pager = $this->getLayout()->createBlock('page/html_pager', 'downloadplus.customer.products.pager');
                }
                $pager->setCollection($this->getItems());
                $this->setChild('pager', $pager);
            } else {
                $this->unsetChild('pager');
            }

            $this->getItems()->load();
            foreach ($this->getItems() as $item) {
                if ($item->getPurchasedId()) {
                    $item->setPurchased($this->getPurchased()->getItemById($item->getPurchasedId()));
                    $item->setProductName($item->getPurchased()->getProductName());
                    $item->setProductSku($item->getPurchased()->getProductSku());
                } else {
                    $data = new Varien_Object();
                    $item->setCustomerId($this->_session->getCustomerId());
                    $orderItem = Mage::getModel('sales/order_item')->load($item->getOrderItemId());
                    if ($orderItem->getId()) {
                        $data->setOrderItemId($orderItem->getId());
                        $data->setOrderId($orderItem->getOrderId());
                        $data->setOrderIncrementId($orderItem->getOrder()->getIncrementId());
                        $data->setProductId($orderItem->getProductId());

                        $product = Mage::getModel('catalog/product')->load($orderItem->getProductId());
                        $item->setProductSku($product->getSku());
                        $item->setProductName($product->getName());
                    }
                    $item->setPurchased($data);
                }
            }
        }

        return $this;
    }

    public function updateCollection()
    {
        $this->setItems(Mage::getResourceModel('downloadplus/link_purchased_item_collection')->addFieldToFilter('item_id', 0));

    	// Downloads related to Downloadable Products & Links unlocked by Serialnumber
        $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
			           ->addFieldToFilter('customer_id', $this->_session->getCustomerId());
        $this->setPurchased($purchased);
        $purchasedIds = $purchased->getAllIds();

        // Downloads related to Order Items
        $orderIds = Mage::getResourceModel('sales/order_collection')
            		->addAttributeToSelect('id')
            		->addAttributeToFilter('customer_id', $this->_session->getCustomerId())
            		->addAttributeToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            		->getAllIds();

        $orderItemIds = Mage::getResourceModel('sales/order_item_collection')
       					->addFieldToFilter('order_id', Array('in'=>$orderIds))
       					->getAllIds();

        if (count($purchasedIds) || count($orderItemIds)) {
	        $items = Mage::getResourceModel('downloadplus/link_purchased_item_collection');

	        $sql = "(";
	        $sql.= "(SELECT UUID() AS id, '".Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_PURCHASED."' AS relates_to,
						item_id,
		        		NULL as order_id,
	  					purchased_id,
						order_item_id,
						product_id,
	        			product.sku AS product_sku,
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
						link_purchased_item.created_at AS created_at,
						link_purchased_item.updated_at AS updated_at,
		        		NULL as attributes
	        		FROM ".$items->getTable('downloadable/link_purchased_item')." AS link_purchased_item
	        		LEFT JOIN ".$items->getTable('catalog/product')." AS product ON link_purchased_item.product_id = product.entity_id
	        		)";
	        $sql.= " UNION ALL ";
	        $sql.= "(SELECT UUID() as id, '".Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_CUSTOMER."' AS relates_to,
						item_id,
		        		NULL as order_id,
	  					purchased_id,
						order_item_id,
						product_id,
	        			product.sku AS product_sku,
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
						link_customer_item.created_at AS created_at,
						link_customer_item.updated_at AS updated_at,
		        		NULL as attributes
	        		FROM ".$items->getTable('downloadplus/link_customer_item')." AS link_customer_item
	        		LEFT JOIN ".$items->getTable('catalog/product')." AS product ON link_customer_item.product_id = product.entity_id
	        		)";
	        if (Mage::helper('downloadplus')->existsDownloadplusBonus()) {
		        $sql.= " UNION ALL ";
		        $sql.= "(SELECT UUID() as id, '".Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_BONUS."' AS relates_to,
							item_id,
			        		order_id,
		  					'0' as purchased_id,
							order_item_id,
							product_id,
		        			product.sku AS product_sku,
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
							link_bonus_item.created_at AS created_at,
							link_bonus_item.updated_at AS updated_at,
			        		attributes
		        		FROM ".$items->getTable('downloadplusbonus/link_purchased_bonus_item')." AS link_bonus_item
		        		LEFT JOIN ".$items->getTable('catalog/product')." AS product ON link_bonus_item.product_id = product.entity_id
        				)";
		    }
	        $sql.= ")";

	        $allItems = new Zend_DB_Expr($sql);

	        $items->getSelect()
	        		->reset()
	        		->from(Array('main_table'=>$allItems));

	        /* Exclude Magazine Addon Links from this Collection */
	        $items->addPurchasedLinksToResult()
			        ->addFieldToFilter('main_table.status', array('nin' => Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT))
			        ->addFieldToFilter('main_table.link_type', array('nin' => explode(',', Pisc_Downloadplus_Helper_Download::FRONTEND_FILTER_LINK_TYPES)));

	        /* Only customer related Ids */
	        /*
	         $items->getSelect()
	         ->where('main_table.purchased_id IN ('.implode(',',$purchasedIds).') OR main_table.order_item_id IN ('.implode(',',$orderItemIds).')');
	         */

	        $filter = Array();
	        if (!empty($purchasedIds)) {
	            $filter[] = 'main_table.purchased_id IN ('.implode(',', $purchasedIds).')';
	        }
	        if (!empty($orderItemIds)) {
	            $filter[] = 'main_table.order_item_id IN ('.implode(',', $orderItemIds).')';
	        }

            $items->getSelect()
                   ->where(implode(' OR ', $filter));

			/* Set Ordering */
	        $items->getSelect()
	          	->order(Array($this->getSortPurchasedItems()));
	        $this->setItems($items);
        }

        return $this;
    }

    /*
     * Adds Sort Condition to Collection
     */
    protected function _addSort($collection, $sortCondition)
    {
         $sort = explode(',', $sortCondition);
         foreach ($sort as $order) {
         	$_order = explode(' ', $order);
         	if (isset($_order[0]) && isset($_order[1])) {
         		$collection->addOrder($_order[0], $order[1]);
         	}
         }

    	return $this;
    }

	/*
	 * Sets a Sort Condition
	 */
	public function setSortProducts($sort) {
		$this->_sortProducts = $sort;
		return $this;
	}
    public function getSortProducts() {
        if ($this->getData('sort_products')) {
            return $this->getData('sort_products');
        }
        return $this->_sortProducts;
    }

	/*
	 * Sets a Sort Condition
	 */
	public function setSortPurchasedItems($sort) {
		$this->_sortPurchasedItems = $sort;
		return $this;
	}
	public function getSortPurchasedItems() {
	    if ($this->getData('sort_purchased_items')) {
	        return $this->getData('sort_purchased_items');
	    }
	    return $this->_sortPurchasedItems;
	}

	/*
	 * Sets a Sort Condition
	 */
	public function setSortVersionHistory($sort) {
		$this->_sortVersionHistory = $sort;
		return $this;
	}
	public function getSortVersionHistory() {
	    if ($this->getData('sort_version_history')) {
	        return $this->getData('sort_version_history');
	    }
	    return $this->_sortVersionHistory;
	}

	/*
	 * Allows to disable Pager from layout.xml
	 */
	public function setPager($value) {
		$this->_pager = $value==='true'? true: false;
	}

    public function getVersionHistory($purchasedItem)
    {
    	$result = Array();

    	if ($purchasedItem->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_PURCHASED) {
			$result = Mage::getModel('downloadplus/download_detail')->getCollection()
						->addLinkToFilter($purchasedItem->getLinkId())
						->addSort($this->getSortVersionHistory())
						->getHistoricalFiles();
    	}

		return $result;
    }

    public function getDetail($item)
    {
    	$result = Mage::getModel('downloadplus/download_detail');
		$config = Mage::getModel('downloadplus/config');

    	if ($item->getLinkType()==Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
    		$file = null;
    		if ($this->isProductRelated($item)) {
    			$file = Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_LINK.$item->getLinkFile();
		    	if ($config->getDownloadableDeliveryProductBehaviour()==Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_LATEST) {
		    		$link = Mage::getModel('downloadable/link')->load($item->getLinkId());
		    		$file = $link->getLinkFile();
		    		$result->loadByFile($file);
		    	}
    		}
    		if ($this->isCustomerRelated($item)) {
    			$file = Pisc_Downloadplus_Model_Download_Detail::TYPE_DOWNLOADABLE_CUSTOMER.$item->getLinkFile();
    			$result->loadByFile($file);
    		}
    	}

    	if ($item->getLinkType()==Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
    		if ($this->isProductRelated($item)) {
		    	$result->loadByLinkId($item->getItemId());
    		}
    		if ($this->isCustomerRelated($item)) {
    			$result->loadByLinkCustomerItemId($item->getItemId());
    		}
    	}

    	if ($this->isBonusRelated($item)) {
    		$result->loadByLinkBonusItemId($item->getLinkId(), Mage::helper('downloadplus')->getStoreId());
    	}

    	return $result;
    }

    public function doesExpire($item)
    {
    	$extension = Mage::getModel('downloadplus/link_extension')
    					->loadByLinkId($item->getLinkId());
    	$result = $extension->doesExpire();
    	return $result;
    }

    public function isExpired($item)
    {
    	$extension = Mage::getModel('downloadplus/link_purchased_item_extension')
    					->loadByPurchasedLink($item);
    	$result = $extension->isExpired();
    	return $result;
    }

    public function getDaysUntilExpiration($item)
    {
    	$extension = Mage::getModel('downloadplus/link_purchased_item_extension')
    					->loadByPurchasedLink($item);
    	$result = $extension->getDaysUntilExpiration();
    	return $result;
    }

    public function getTextCurrent()
    {
    	$config = Mage::getModel('downloadplus/config');
    	if ($config->getDownloadableDeliveryProductBehaviour()==Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_LATEST) {
    		return $this->__('Current Release');
    	} else {
    		return $this->__('Purchased Release');
    	}
    }

    public function formatTimestamp($date=null, $format='short', $showTime=false)
    {
    	if ($date) {
    		$zendDate = new Zend_Date($date);
    		return parent::formatDate($zendDate, $format, $showTime);
    	}
    	return '';
    }

    public function getOrderViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $orderId));
    }

    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    public function getRemainingDownloads($item)
    {
        if ($item->getNumberOfDownloadsBought()) {
            $downloads = $item->getNumberOfDownloadsBought() - $item->getNumberOfDownloadsUsed();
            return $downloads;
        }
        return Mage::helper('downloadable')->__('Unlimited');
    }

    public function getDownloadUrl($item)
    {
    	$result = null;

		$config = Mage::getModel('downloadplus/config');
		$params = array('id' => $item->getLinkHash());

		if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

    	if ($item->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_PURCHASED) {
    		$result = $this->getUrl('downloadable/download/link', $params);
    	}

    	if ($item->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_CUSTOMER) {
    		$result = $this->getUrl('downloadable/download/customer', $params);
    	}

    	if ($item->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_BONUS) {
    		$result = $this->getUrl('downloadable/download/bonus', $params);
    	}

        return $result;
    }

    /*
     * Returns TRUE when item is product related
     */
    public function isProductRelated($item)
    {
    	$result = ($item->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_PURCHASED);
    	return $result;
    }

    /*
     * Returns TRUE when item is customer related
     */
    public function isCustomerRelated($item)
    {
    	$result = ($item->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_CUSTOMER);
    	return $result;
    }

    /*
     * Returns TRUE when item is customer related
     */
    public function isBonusRelated($item)
    {
    	$result = (Mage::helper('downloadplus')->existsDownloadplusBonus() && $item->getRelatesTo()==Pisc_Downloadplus_Model_Download_Detail::TYPE_SOURCE_BONUS);
    	return $result;
    }

	/*
	 * Returns a Download Link for a particular archived file
	 */
	function getArchiveDownloadUrl($item, $detail)
	{
		$config = Mage::getModel('downloadplus/config');
		$params = array('id' => $item->getLinkHash());

		if ($config->isDownloadForceSecure()) {
			$params['_secure'] = true;
		}

		if ($detail instanceof Pisc_Downloadplus_Model_Download_Detail) {
			$params['archive'] = $detail->getId();
		} else {
			$params['archive'] = $detail;
		}

		return $this->getUrl('downloadable/download/link', $params);
	}

	/**
	 * Returns if the RSS Feed is available
	 */
	public function isRssAvailable()
	{
		$config = Mage::getModel('downloadplus/config');
		return $config->isDownloadableRssFeed();
	}

	/**
	 * Returns the Link to the RSS Feed of the Version History
	 */
	public function getRssLink()
	{
		return $this->getUrl('downloadable/rss/updates');
	}

    /**
     * Return true if target of link new window
     */
    public function getIsOpenInNewWindow()
    {
        return Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }

}
