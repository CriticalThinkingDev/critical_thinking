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


class AW_Marketsuite_Model_Rule_Condition_Product_Producthistory extends Mage_Rule_Model_Condition_Combine
{
    protected $orderedCountCache = array();
    protected $_productResouce = null;

    /**
     *
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_product_producthistory')
            ->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            if (preg_match('/^(1.8|1.9|1.1[0-9]|1.4.1|1.4.2|1.5|1.6|1.7)/', Mage::getVersion())) {
                if (!$attribute->isAllowedForRuleCondition() || !$attribute->getIsUsedForPromoRules())
                    continue;
            }
            else {
                if (!$attribute->isAllowedForRuleCondition() || !$attribute->getIsUsedForPriceRules())
                    continue;
            }
            if (Mage::helper('marketsuite')->CheckUselessProductAttributes($attribute->getFrontendLabel()))
                continue;

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $leftattributes = array();
        foreach ($attributes as $code => $label) {
            $leftattributes[] = array('value' => 'marketsuite/rule_condition_product_producthistory_conditions|' . $code, 'label' => $label);
        }

        $conditions = Mage_Rule_Model_Condition_Combine::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, $leftattributes);

        /* Add category as attribute to product attributes */
        $conditions[] = array(
            'value' => 'marketsuite/rule_condition_product_producthistory_conditions|category',
            'label' => Mage::helper('marketsuite')->__('Category')
        );

        return $conditions;
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(array(
            'viewed' => $hlp->__('viewed'),
            'ordered' => $hlp->__('ordered'),
        ));
        return $this;
    }

    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '==' => Mage::helper('rule')->__('for'),
            '>' => Mage::helper('rule')->__('more than'),
            '<' => Mage::helper('rule')->__('less than'),
        ));
        return $this;
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            Mage::helper('salesrule')->__("If Product was %s %s %s times and matches %s of these conditions:", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml()
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function getProductResouce()
    {

        if (null === $this->_productResouce) {
            $this->_productResouce = Mage::getResourceModel('catalog/product');
        }

        return $this->_productResouce;
    }

    public function validate(Varien_Object $object)
    {
        if ($object instanceof AW_Marketsuite_Model_Cart_Abstract) {
            $object = $object->getCustomer();
        }
        if ($object instanceof AW_Marketsuite_Model_Order_Abstract) {
            if ($this->getAttribute() == 'viewed') {
                $productsViewed = $object->getProductsViewed();
                foreach ($productsViewed as $product)
                    if (parent::validate($product) == (bool)$this->getValue())
                        return $this->validateAttribute($product->getViewsCount());
                return false;
            }
            if ($this->getAttribute() == 'ordered') {
                if (!isset($this->orderedCountCache[$object->getEntityId()])) {
                    $customersOrders = $object->getOrderId();
                    $count = 0;
                    if ($customersOrders instanceof Traversable || is_array($customersOrders)) {
                        foreach ($customersOrders as $order)
                        {
                            if ($order->getOrderStatus() != Mage_Sales_Model_Order::STATE_COMPLETE) continue;
                            foreach ($order->getProducts() as $item)
                                if (parent::validate($item) == (bool)$this->getValue())
                                    $count += $order->getProductQtyOrdered($item->getEntityId());
                        }
                    }
                    $this->orderedCountCache[$object->getEntityId()] = $count;
                }
                return $this->validateAttribute($this->orderedCountCache[$object->getEntityId()]);
            }
        }
        if ($object instanceof AW_Marketsuite_Model_Customer_Abstract && $object->getEntityId()) {
            if ($this->getAttribute() == 'viewed') {
                $productsViewed = $object->getProductsViewed();
                foreach ($productsViewed as $product)
                    if (parent::validate($product) == (bool)$this->getValue())
                        return $this->validateAttribute($product->getViewsCount());
                return false;
            }
            if ($this->getAttribute() == 'ordered') {
                //if (!isset($this->orderedCountCache[$object->getEntityId()])) {
                    $customersOrders = $object->getOrders();
                    $count = 0;
                    foreach ($customersOrders as $order)
                    {
                        if ($order->getOrderStatus() != Mage_Sales_Model_Order::STATE_COMPLETE) continue;
                        foreach ($order->getProducts() as $item) {
                            $_check = $item->getData();

                            if (isset($_check['entity_id'])) {
                                if (parent::validate($item) == (bool)$this->getValue())
                                    $count += $order->getProductQtyOrdered($item->getEntityId());
                            }
                        }
                    }

                    $this->orderedCountCache[$object->getEntityId()] = $count;
               // }
                return $this->validateAttribute($this->orderedCountCache[$object->getEntityId()]);
            }
        }
        return false;
    }
}
