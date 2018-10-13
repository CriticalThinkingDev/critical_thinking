<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    1.2.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


abstract class AW_Marketsuite_Model_Order_Abstract extends AW_Marketsuite_Model_Object
{
    /**
     * Order item table name
     * @var string
     */
    protected $order_item_table;
    protected static $instance;
    protected static $customersCache = array();

    /** Products which have zero qty in sales_flat_order_item table */
    const PRODUCT_TYPES_WITH_ZERO_QTY = 'bundle,grouped';

    public function __construct()
    {
        parent::__construct();
        $this->select = $this->conn_read->select()->from($this->resource->getTableName('sales/order'));
        $this->order_item_table = $this->resource->getTableName('sales/order_item');
    }

    /**
     * Getting instance of order model according to magento version.
     * Default = mag13. Magento 1.8.*, 1.4.* (without 1.4.0.*) = mag18. Magento 1.4.0.* = mag14
     * @return mixed
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $magentoVersion = Mage::getVersion();
            $preffix = 'mag13';
            if (preg_match('/^(1.9|1.8|1.1[0-9]|1.4|1.5|1.6|1.7)/', $magentoVersion))
                $preffix = 'mag18';
            if (preg_match('/^1.4.0/', $magentoVersion))
                $preffix = 'mag14';
            self::$instance = Mage::getModel('marketsuite/order_' . $preffix);
        }
        return clone self::$instance;
    }

    /**
     * Checking MSS rule for order
     * @param AW_Marketsuite_Model_Filter $rule
     * @return boolean
     */
    public function checkRule($rule)
    {
        if (!$rule->getIsActive())
            return false;

        return $rule->validate($this);
    }

    /**
     * Getting array of orders ids, which matches to rule
     * @param AW_Marketsuite_Model_Filter $rule
     * @return array
     */
    public function getMatches($rule)
    {
        $query = $this->conn_read->query($this->getSelect());

        $matchesArray = array();
        
        foreach ($query->fetchAll() as $row) {
            $order = clone $this;
            $order->addData($row)->addAdditionalParams();
            if ($order->checkRule($rule)) {
                $matchesArray[] = $order->getEntityId();
            }
        }

        return $matchesArray;
    }

    /**
     * TODO grouped = summ of grouped, bundle = bundle  sum in it
     * Return qty of ordered products
     * @return int
     */
    public function getQtyOrdered()
    {
        if (!isset(self::$selectCache['order_qty_ordered'])) {
            $select = $this->conn_read
                ->select()
                ->from($this->order_item_table, array('sum' => 'sum(qty_ordered)'))
                ->where("order_id = ?", 'order_id_replace')
                ->where("FIND_IN_SET('product_type', ?)=0", self::PRODUCT_TYPES_WITH_ZERO_QTY);
            self::$selectCache['order_qty_ordered'] = $select->assemble();
        }
        $select = str_replace('order_id_replace', $this->getEntityId(), self::$selectCache['order_qty_ordered']);
        $result = $this->conn_read->query($select)->fetch();
        if (isset($result['sum']))
            return $result['sum'];
        return 0;
    }

    /**
     * Return qty of ordered product
     * @param int $productId
     * @return int
     */
    public function getProductQtyOrdered($productId)
    {
        if (!isset(self::$selectCache['order_product_qty_ordered'])) {
            $select = $this->conn_read
                ->select()
                ->from($this->order_item_table, array('sum' => 'sum(qty_ordered)'))
                ->where("order_id = ?", 'order_id_replace')
                ->where('product_id = ?', 'product_id_replace')
                ->where("FIND_IN_SET('product_type', ?)=0", self::PRODUCT_TYPES_WITH_ZERO_QTY);
            self::$selectCache['order_product_qty_ordered'] = $select->assemble();
        }
        $search = array('order_id_replace', 'product_id_replace');
        $replace = array($this->getEntityId(), $productId);
        $select = str_replace($search, $replace, self::$selectCache['order_product_qty_ordered']);
        $result = $this->conn_read->query($select)->fetch();
        if (isset($result['sum']))
            return $result['sum'];
        return 0;
    }

    /**
     * Return customer orders
     * @param int $customerId
     * @return array
     */
    public function getOrdersByCustomerId($customerId)
    {
        if (!isset(self::$selectCache['orders_by_customer_id'])) {
            self::$selectCache['orders_by_customer_id'] =
                $this->getSelect()->where('customer_id = ?', 'customerIdToReplace')
                    ->assemble();
        }
        $ordersArray = $this->conn_read
            ->query(str_replace('customerIdToReplace', $customerId, self::$selectCache['orders_by_customer_id']))
            ->fetchAll();
        $orders = array();
        foreach ($ordersArray as $order) {
            $newOrder = clone $this;
            $newOrder->addData($order)->addAdditionalParams();
            $orders[] = $newOrder;
        }
        return $orders;
    }

    /**
     * Return products in order
     * @return array
     */
    public function getProducts()
    {
        if (!isset(self::$selectCache['order_products_select'])) {
            $select = $this->conn_read
                ->select()
                ->from($this->order_item_table, array('product_id'))
                ->where('order_id = ?', 'order_id_replace');
            self::$selectCache['order_products_select'] = $select->assemble();
        }
        $select = str_replace('order_id_replace', $this->getEntityId(), self::$selectCache['order_products_select']);
        $productsIds = $this->conn_read->fetchCol($select);

        $products = array();
        foreach ($productsIds as $product)
            $products[] = AW_Marketsuite_Model_Product_Abstract::getInstance()->load($product);

        return $products;
    }

    /**
     * Get customer for the order
     * @return mixed
     */
    public function getCustomer()
    {
        if (!isset(self::$customersCache[$this->getCustomerId()]))
            self::$customersCache[$this->getCustomerId()] = AW_Marketsuite_Model_Customer_Abstract::getInstance()->load($this->getCustomerId());
        return self::$customersCache[$this->getCustomerId()];
    }

    /**
     * Get Order by Id
     * @param int $orderId
     * @return AW_Marketsuite_Model_Order_Abstract
     */
    public function load($orderId)
    {
        if (!isset(self::$selectCache['order_select'])) {
            self::$selectCache['order_select'] =
                $this->getSelect()->where('entity_id = ?', 'order_id_replace')
                    ->assemble();
        }
        $select = str_replace('order_id_replace', $orderId, self::$selectCache['order_select']);
        $order = $this->conn_read->fetchRow($select);

        if ($order)
            $this->addData($order)->addAdditionalParams();
        return $this;
    }

    /**
     * Adding additional parameters to order object
     * @return AW_Marketsuite_Model_Order_Abstract
     */
    public function addAdditionalParams()
    {

        $this->setOrderStoreId((array)$this->getStoreId());
        $this->setOrderStatus($this->getState());
        $this->setOrderDate(substr($this->getCreatedAt(), 0, 10));

        return $this;
    }

    public function getOrderId()
    {
        return AW_Marketsuite_Model_Order_Abstract::getInstance()->getOrderById($this->getEntityId());
    }
}
