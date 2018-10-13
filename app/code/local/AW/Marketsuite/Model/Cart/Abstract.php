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

abstract class AW_Marketsuite_Model_Cart_Abstract extends AW_Marketsuite_Model_Object
{
    protected static $instance;
    /**
     * Getting instance of cart model according to magento version
     * @return mixed 
     */
    public static function getInstance()
    {
        if (!self::$instance) self::$instance = Mage::getModel('marketsuite/cart_mag13');
        return clone self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        $this->select = $this->conn_read->select()->from($this->resource->getTableName('sales/quote'))
                ->where('is_active = 1')->order('updated_at desc')->limit(1);
    }

    /**
     * Getting products in cart
     * @return array
     */
    public function getProducts()
    {
        if (is_null($this->getEntityId())) return array();

        if (!isset(self::$selectCache['products_in_cart']))
        {
            $select = $this->conn_read->select()
                            ->from(array('quote_item_table' => $this->resource->getTableName('sales/quote_item')), array('product_id'))
                            ->where('quote_id = ?', 'cart_id_replace');
            self::$selectCache['products_in_cart'] = $select->assemble();
        }
        $select = str_replace('cart_id_replace', $this->getEntityId(), self::$selectCache['products_in_cart']);
        $productIds = $this->conn_read->fetchCol($select);

        $products = array();
        foreach ($productIds as $product)
            $products[] = AW_Marketsuite_Model_Product_Abstract::getInstance()->load($product);

        return $products;
    }

    /**
     * Adding additional parameters to cart object
     * @return AW_Marketsuite_Model_Cart_Abstract
     */
    public function addAdditionalParams()
    {
        $this->setNumberofitems($this->getItemsQty() ? $this->getItemsQty() : 0);
        $this->setGrandtotal($this->getGrandTotal() ? $this->getGrandTotal() : null);
        $this->getSubtotal($this->getSubtotal() ? $this->getSubtotal() : null);

        return $this;
    }

    /**
     * Getting customer cart
     * @param int $customerId
     * @return AW_Marketsuite_Model_Cart_Abstract 
     */
    public function getCartByCustomerId($customerId)
    {
        if (!isset(self::$selectCache['cart_by_customer_id']))
        {
            self::$selectCache['cart_by_customer_id'] =
                $this->getSelect()->where('customer_id = ?', 'customer_id_replace')
                    ->assemble();
        }
        $select = str_replace('customer_id_replace', $customerId, self::$selectCache['cart_by_customer_id']);
        $quote = $this->conn_read->fetchRow($select);
        if ($quote) $this->addData($quote);
        $this->addAdditionalParams();
        return $this;
    }

    /**
     * Getting cart by id
     * @param int $quoteId
     * @return AW_Marketsuite_Model_Cart_Abstract 
     */
    public function load($quoteId)
    {
        if (!isset(self::$selectCache['quote_select']))
        {
            self::$selectCache['quote_select'] =
                $this->getSelect()->where('entity_id = ?', 'quote_id_replace')
                    ->assemble();
        }
        $select = str_replace('quote_id_replace', $quoteId, self::$selectCache['quote_select']);
        $quote = $this->conn_read->fetchRow($select);
        if ($quote) $this->addData($quote);
        $this->addAdditionalParams();
        return $this;
    }

    /**
     * Getting customer for the cart
     * @return mixed 
     */
    public function getCustomer()
    {
        return AW_Marketsuite_Model_Customer_Abstract::getInstance()->load($this->getCustomerId());
    }
}