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


class AW_Marketsuite_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $attributes_order = array();
        $attributes_order[] = array('value' => 'marketsuite/rule_condition_order_numberorders', 'label' => Mage::helper('salesrule')->__('Number of orders'));
        $attributes_order[] = array('value' => 'marketsuite/rule_condition_order_salesamount', 'label' => Mage::helper('salesrule')->__('Sales amount'));
        $attributes_order[] = array('value' => 'marketsuite/rule_condition_order_purchasedquantity', 'label' => Mage::helper('salesrule')->__('Purchased quantity'));

        $attributes_customer = array();
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_address|billing', 'label' => Mage::helper('salesrule')->__('Billing address'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_address|shipping', 'label' => Mage::helper('salesrule')->__('Shipping address'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_conditions|group_id', 'label' => Mage::helper('salesrule')->__('Customer group'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_conditions|dob', 'label' => Mage::helper('salesrule')->__('Date of birth'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_conditions|email', 'label' => Mage::helper('salesrule')->__('Email'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_conditions|firstname', 'label' => Mage::helper('salesrule')->__('First name'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_conditions|lastname', 'label' => Mage::helper('salesrule')->__('Last name'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_conditions|gender', 'label' => Mage::helper('salesrule')->__('Gender'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_conditions|newslettersubscription', 'label' => Mage::helper('salesrule')->__('Newsletter subscription'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_customer_conditions|annewslettersubscription', 'label' => Mage::helper('salesrule')->__('Advanced newsletter subscription'));
        $attributes_customer[] = array('value' => 'marketsuite/rule_condition_store_list', 'label' => Mage::helper('salesrule')->__('Registered in store'));

        $attributes_shoppingcart = array();
        $attributes_shoppingcart[] = array('value' => 'marketsuite/rule_condition_shoppingcart_conditions|grandtotal', 'label' => Mage::helper('salesrule')->__('Grand total'));
        $attributes_shoppingcart[] = array('value' => 'marketsuite/rule_condition_shoppingcart_conditions|numberofitems', 'label' => Mage::helper('salesrule')->__('Number of items'));
        $attributes_shoppingcart[] = array('value' => 'marketsuite/rule_condition_shoppingcart_conditions|subtotal', 'label' => Mage::helper('salesrule')->__('Subtotal'));

        $attributes_products = array();
        $attributes_products[] = array('value' => 'marketsuite/rule_condition_product_productlist', 'label' => Mage::helper('salesrule')->__('Product List'));
        $attributes_products[] = array('value' => 'marketsuite/rule_condition_product_producthistory', 'label' => Mage::helper('salesrule')->__('Product History'));

        $attributes_stores = array();
        $attributes_stores[] = array('value' => 'marketsuite/rule_condition_store_list', 'label' => Mage::helper('salesrule')->__('Related store'));

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'marketsuite/rule_condition_combine', 'label' => Mage::helper('catalogrule')->__('Conditions Combination')),
            array('label' => Mage::helper('marketsuite')->__('Orders'), 'value' => $attributes_order),
            array('label' => Mage::helper('marketsuite')->__('Customers'), 'value' => $attributes_customer),
            array('label' => Mage::helper('marketsuite')->__('Shopping Cart'), 'value' => $attributes_shoppingcart),
            array('label' => Mage::helper('marketsuite')->__('Products'), 'value' => $attributes_products),
            //array('label' => Mage::helper('marketsuite')->__('Store rules'), 'value' => $attributes_stores),
        ));
        return $conditions;
    }
}
