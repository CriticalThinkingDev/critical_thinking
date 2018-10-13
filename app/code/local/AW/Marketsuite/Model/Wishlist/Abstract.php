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

abstract class AW_Marketsuite_Model_Wishlist_Abstract extends AW_Marketsuite_Model_Object
{
    protected static $instance;
    /**
     * Getting instance of wishlist model according to magento version
     * @return mixed 
     */
    public static function getInstance()
    {
        if(!self::$instance) self::$instance = Mage::getModel('marketsuite/wishlist_mag13');
        return clone self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        $this->select = $this->conn_read->select()->from($this->resource->getTableName('wishlist/wishlist'));
    }
    
    /**
     * Return products in wishlist
     * @return array
     */
    public function getProducts()
    {
        if (is_null($this->getWishlistId())) return array();
        if (!isset(self::$selectCache['products_in_wishlist']))
        {
            $select = $this->conn_read->select()
                        ->from(array('wishlist_item_table' => $this->resource->getTableName('wishlist/item')), array('product_id'))
                        ->where('wishlist_id = ?', 'wishlist_id_replace');
            self::$selectCache['products_in_wishlist'] = $select->assemble();
        }
        $select = str_replace('wishlist_id_replace', $this->getWishlistId(), self::$selectCache['products_in_wishlist']);
        $productIds = $this->conn_read->fetchCol($select);

        $products = array();
        foreach ($productIds as $product)
            $products[] = AW_Marketsuite_Model_Product_Abstract::getInstance()->load($product);

        return $products;
    }

    /**
     * Return customer for wishlist
     * @return mixed
     */
    public function getCustomer()
    {
        return AW_Marketsuite_Model_Customer_Abstract::getInstance()->load($this->getCustomerId());
    }

    /**
     * Return wishlist by customer id
     * @param int $customerId
     * @return AW_Marketsuite_Model_Wishlist_Abstract
     */
    public function getWishlistByCustomerId($customerId)
    {
        if (!isset(self::$selectCache['wishlist_by_customer_id']))
        {
            self::$selectCache['wishlist_by_customer_id'] = 
                $this->getSelect()->where('customer_id = ?', 'customer_id_replace')
                    ->assemble();
        }
        $select = str_replace('customer_id_replace', $customerId, self::$selectCache['wishlist_by_customer_id']);
        $wishlist = $this->conn_read->fetchRow($select);
        if ($wishlist) $this->addData($wishlist);
        return $this;
    }

    /**
     * Loading wishlist by Id
     * @param int $wishlistId
     * @return AW_Marketsuite_Model_Wishlist_Abstract
     */
    public function load($wishlistId)
    {
        $wishlist = $this->conn_read->fetchRow($this->getSelect()->where('wishlist_id = ?', $wishlistId));
        if ($wishlist) $this->addData($wishlist);
        return $this;
    }
}