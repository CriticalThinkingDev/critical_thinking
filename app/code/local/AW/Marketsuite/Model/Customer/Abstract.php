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

abstract class AW_Marketsuite_Model_Customer_Abstract extends AW_Marketsuite_Model_Object
{
    /** Return default billing address type name */
    const DEFAULT_BILLING_ADDRESS = 'default_billing';
    /** Return default shipping address type name */
    const DEFAULT_SHIPPING_ADDRESS = 'default_shipping';

    /**
     * Customer table name
     * @var string
     */
    protected $customer_table;
    /**
     * Customer address table name
     * @var string
     */
    protected static $_attributesCache;

    protected static $instance;

    public function __construct()
    {
        parent::__construct();
        $this->customer_table = $this->resource->getTableName('customer/entity');
        $this->select = $this->conn_read->select()->from($this->customer_table);
        $this
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('gender')
            ->addAttributeToSelect('dob');
    }

    /**
     * Getting instance of customer model according to magento version
     * @return mixed
     */
    public static function getInstance()
    {
        if (!self::$instance) self::$instance = Mage::getModel('marketsuite/customer_mag13');
        return clone self::$instance;
    }

    /**
     * Checking MSS rule for customer
     * @param AW_Marketsuite_Model_Filter $rule
     * @return boolean
     */
    public function checkRule($rule)
    {
        if (!$rule->getIsActive()) return false;
        return $rule->validate($this);
    }

    /**
     * Getting products viewed by customer
     * @return array
     */
    public function getProductsViewed()
    {
        if (!isset(self::$selectCache['products_viewed'])) {
            $_visitorTable = $this->resource->getTableName('log/visitor');
            $_urlTable = $this->resource->getTableName('log/url_table');
            $_urlInfoTable = $this->resource->getTableName('log/url_info_table');
            $_customerTable = $this->resource->getTableName('log/customer');

            $select = $this->conn_read->select();

            /* TODO optimize selection. Remove useless variables from query  */
            $select->from($_customerTable)
                ->joinInner($_visitorTable, $_visitorTable . '.visitor_id=' . $_customerTable . '.visitor_id')
                ->joinInner($_urlTable, $_urlTable . '.visitor_id=' . $_visitorTable . '.visitor_id')
                ->joinInner($_urlInfoTable, $_urlTable . '.url_id=' . $_urlInfoTable . '.url_id')
                ->where($_urlInfoTable . '.url LIKE "%catalog/product/view/id/%"')
                ->where($_customerTable . '.customer_id = ?', 'customer_id_replace');

            self::$selectCache['products_viewed'] = $select->assemble();
        }
        $select = str_replace('customer_id_replace', $this->getEntityId(), self::$selectCache['products_viewed']);

        $data = $this->conn_read->fetchAll($select);

        $product_ids = array();

        foreach ($data as $row)
        {
            preg_match('/.*catalog\/product\/view\/id\/(\d+).*/', $row['url'], $my);
            $product_ids[] = $my[1];
        }

        $counts = array();
        $counts = array_count_values($product_ids);

        $products = array();
        foreach ($counts as $key => $count)
            $products[] = AW_Marketsuite_Model_Product_Abstract::getInstance()->load($key)->setViewsCount($count);

        return $products;
    }

    /**
     * Getting array of customers ids, which matches to rule
     * @param AW_Marketsuite_Model_Filter $rule
     * @return array
     */
    public function getMatches($rule)
    {
        $query = $this->conn_read->query($this->getSelect());
        $matchesArray = array();
        foreach ($query->fetchAll() as $row) {
            $customer = clone $this;
            $customer->addData($row)->formatData();
            if ($customer->checkRule($rule)) {
                $matchesArray[] = $customer->getEntityId();
            }
        }
        return $matchesArray;
    }

    /**
     * Adding attribute to $this->select variable
     * @param string $attributeCode
     * @return AW_Marketsuite_Model_Customer_Abstract
     */
    public function addAttributeToSelect($attributeCode)
    {
        if (!isset(self::$_attributesCache[$attributeCode])) {
            $attribute = Mage::getModel('customer/customer')->getResource()->getAttribute($attributeCode);
            if (!$attribute) return;
            self::$_attributesCache[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'backend_type' => $attribute->getBackendType()
            );
        }

        $attribute = self::$_attributesCache[$attributeCode];

        $select = $this->select;

        $select->joinLeft(
            array('t1_' . $attributeCode => $attribute['table']),
            $this->customer_table . '.entity_id=t1_' . $attributeCode . '.entity_id
                    AND t1_' . $attributeCode . '.attribute_id=' . $attribute['attribute_id'],
            array($attributeCode => 'value')
        );

        return $this;
    }

    /**
     * Format object data (Formating dates to right format, for example)
     */
    public function formatData()
    {
        if ($this->getDob()) $this->setDob(date('Y-m-d', strtotime($this->getDob())));
    }

    /**
     * Getting customer by id
     * @return AW_Marketsuite_Model_Customer_Abstract
     */
    public function load($customerId)
    {
        if (!isset(self::$selectCache['customer_select'])) {
            self::$selectCache['customer_select'] =
                $this->getSelect()->where($this->customer_table . '.entity_id = ?', 'customer_id_replace')
                    ->assemble();
        }
        $select = str_replace('customer_id_replace', $customerId, self::$selectCache['customer_select']);
        $customer = $this->conn_read->fetchRow($select);
        if ($customer) $this->addData($customer)->formatData();
        return $this;
    }

    /**
     * Getting customer billing or shipping address according to $type param
     * @param string $type
     * @return Varien_object
     */
    public function getAddress($type)
    {
        if (!isset(self::$selectCache['customer_address'])) {
            $eav_attribute_table = $this->resource->getTableName('eav/attribute');
            $customer_address_table = $this->resource->getTableName('customer/address_entity');

            $customerAddressSelect = $this->conn_read
                ->select()
                ->from(array('customer_int' => $this->customer_table . '_int'), array())
                ->joinInner(array('eav_attribute' => $eav_attribute_table),
                'customer_int.attribute_id = eav_attribute.attribute_id', array())
                ->joinInner(array('customer_address_char' => $customer_address_table . '_varchar'),
                'customer_int.value = customer_address_char.entity_id', array('value'))
                ->joinInner(array('eav_attribute1' => $eav_attribute_table),
                'customer_address_char.attribute_id = eav_attribute1.attribute_id', array('attribute_code'))
                ->where('eav_attribute.attribute_code = ?', 'address_type_replace')
                ->where('customer_int.entity_id = ?', 'customer_id_replace');
            self::$selectCache['customer_address'] = $customerAddressSelect->assemble();
        }
        $search = array('address_type_replace', 'customer_id_replace');
        $replace = array($type, $this->getEntityId());
        $select = str_replace($search, $replace, self::$selectCache['customer_address']);
        $customerAddressArray = $this->conn_read->query($select)->fetchAll();

        $customerAddress = new Varien_Object();
        foreach ($customerAddressArray as $item)
            $customerAddress->setData($item['attribute_code'], $item['value']);

        return $customerAddress;
    }

    /**
     * Getting customer orders
     * @return mixed
     */
    public function getOrders()
    {
        return AW_Marketsuite_Model_Order_Abstract::getInstance()->getOrdersByCustomerId($this->getEntityId());
    }

    /**
     * Getting customer cart
     * @return mixed
     */
    public function getCart()
    {
        return AW_Marketsuite_Model_Cart_Abstract::getInstance()->getCartByCustomerId($this->getEntityId());
    }

    /**
     * Getting customer wishlist
     * @return mixed
     */
    public function getWishlist()
    {
        return AW_Marketsuite_Model_Wishlist_Abstract::getInstance()->getWishlistByCustomerId($this->getEntityId());
    }

    /**
     * Adding subscription information to customer
     * @return AW_Marketsuite_Model_Customer_Abstract
     */
    public function addSubscriptionInfo()
    {
        if (!isset(self::$selectCache['newsletter_subscription'])) {
            $newsletter_subscriber_table = $this->resource->getTableName('newsletter/subscriber');

            $newsletterSubscriberSelect = $this->conn_read
                ->select()
                ->from(array('newsletter_subscriber' => $newsletter_subscriber_table))
                ->where('newsletter_subscriber.customer_id = ?', 'customer_id_replace')
                ->where('subscriber_status = ?', Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
            self::$selectCache['newsletter_subscription'] = $newsletterSubscriberSelect->assemble();
        }
        $select = str_replace('customer_id_replace', $this->getEntityId(), self::$selectCache['newsletter_subscription']);
        $newsletterSubscriber = $this->conn_read->query($select)->fetch();
        if (isset($newsletterSubscriber['subscriber_id'])) $this->setNewslettersubscription(true);
        else $this->setNewslettersubscription(false);

        if (Mage::helper('marketsuite')->extensionEnabled('AW_Advancednewsletter')) {
            if (!isset(self::$selectCache['advancednewsletter_subscription'])) {
                if (version_compare(Mage::helper('marketsuite')->getAdvancedNewsletterVersion(), '2.0.0') >= 0)
                    $an_subscriber_table = $this->resource->getTableName('advancednewsletter/subscriber');
                else
                    $an_subscriber_table = $this->resource->getTableName('advancednewsletter/subscriptions');
                $ANSubscriberSelect = $this->conn_read
                    ->select()
                    ->from(array('ANsubscriber' => $an_subscriber_table))
                    ->where('ANsubscriber.email = ?', 'customer_email_replace');
                self::$selectCache['advancednewsletter_subscription'] = $ANSubscriberSelect->assemble();
            }
            $select = str_replace('customer_email_replace', $this->getEmail(), self::$selectCache['advancednewsletter_subscription']);
            $ANSubscriber = $this->conn_read->query($select)->fetch();
            if (isset($ANSubscriber['segments_codes'])) $this->setAnnewslettersubscription(explode(',', $ANSubscriber['segments_codes']));
        }
    }
}
