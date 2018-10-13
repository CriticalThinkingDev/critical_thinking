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

class AW_Marketsuite_Model_Rule_Condition_Order_Numberorders extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_order_numberorders')
            ->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        $conditions = Mage_Rule_Model_Condition_Combine::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'marketsuite/rule_condition_order_numberorders_conditions|order_status', 'label' => Mage::helper('salesrule')->__('Order status')),
            array('value' => 'marketsuite/rule_condition_order_numberorders_conditions|order_date', 'label' => Mage::helper('salesrule')->__('Order date')),
            array('value' => 'marketsuite/rule_condition_order_numberorders_conditions|order_store_id', 'label' => Mage::helper('salesrule')->__('Store')),
        ));
        return $conditions;
    }

    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);

        parent::loadArray($arr, $key);
        return $this;
    }

    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(array(
            'orders_number' => $hlp->__('Orders number'),
        ));
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '==' => Mage::helper('rule')->__('is'),
            '!=' => Mage::helper('rule')->__('is not'),
            '>=' => Mage::helper('rule')->__('equals or greater than'),
            '<=' => Mage::helper('rule')->__('equals or less than'),
            '>' => Mage::helper('rule')->__('greater than'),
            '<' => Mage::helper('rule')->__('less than'),
        ));
        return $this;
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            Mage::helper('salesrule')->__("%s %s %s while %s of these conditions match:",
                $this->getAttributeElement()->getHtml(),
                $this->getOperatorElement()->getHtml(),
                $this->getValueElement()->getHtml(),
                $this->getAggregatorElement()->getHtml()
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    const CACHE_KEY = 'awmsscc999';

    public function validate(Varien_Object $object)
    {
        if ($object instanceof AW_Marketsuite_Model_Order_Abstract)
            return false;

        if ($object instanceof AW_Marketsuite_Model_Cart_Abstract)
            $object = $object->getCustomer();

        if ($object instanceof AW_Marketsuite_Model_Customer_Abstract) {
            $customersOrders = $object->getOrders();
            $cache = Mage::app()->getCacheInstance();
            $count = 0;
            $all = $this->getAggregator() === 'all';
            $true = (bool)$this->getValue();
            if ($collection = $cache->load(self::CACHE_KEY)) {
                $matchesArray = explode(',', $collection);
            } else {
                $matchesArray = array();
            }

            foreach ($customersOrders as $order) {
                $result = true;
                foreach ($this->getConditions() as $cond) {
                    if ($cond instanceof AW_Marketsuite_Model_Rule_Condition_Order_Numberorders_Conditions) {
                        $validated = $cond->validate($order);
                        if (!$validated && $all) {
                            $result = false;
                            break;
                        } elseif (!$validated) {
                            $result = false;
                        } else {
                            $result = true;
                        }
                    }
                }
                if ($result) {
                    $matchesArray[] = $order->getData('entity_id');
                    $cache->save(implode(',', $matchesArray), self::CACHE_KEY);
                    if (parent::validate($order) && $all) {
                        $count++;
                    }
                }
            }
            return $this->validateAttribute($count);
        }
    }
}
