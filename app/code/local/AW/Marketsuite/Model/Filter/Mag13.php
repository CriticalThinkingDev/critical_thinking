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

class AW_Marketsuite_Model_Filter_Mag13 extends AW_Marketsuite_Model_Filter
{
    /**
     * Check $object via MSS rule
     * @param Mage_Sales_Model_Order | Mage_Customer_Model_Customer | Mage_Sales_Model_Quote $object
     * @param int $ruleId
     * @return boolean
     */
    public function checkRule($object, $ruleId)
    {
        if (!$ruleId||!($object instanceof Varien_Object)) return false;
        $rule = $this->load($ruleId);
        if (!$rule->getIsActive()) return false;
        
        if ($object instanceof Mage_Sales_Model_Order) $objectToValidate = AW_Marketsuite_Model_Order_Abstract::getInstance()->load($object->getId());
        else if ($object instanceof Mage_Customer_Model_Customer) $objectToValidate = AW_Marketsuite_Model_Customer_Abstract::getInstance()->load($object->getId());
        else if ($object instanceof Mage_Sales_Model_Quote) $objectToValidate = AW_Marketsuite_Model_Cart_Abstract::getInstance()->load($object->getId());
        else Mage::throwException(Mage::helper('marketsuite')->__('Unknown object model type'));

        return $rule->validate($objectToValidate);
    }

    /**
     * Return customers collection which satisfy MSS rule requirements
     * @param int $ruleId
     * @return Mage_Customer_Model_Entity_Customer_Collection
     */
    public function exportCustomers($ruleId = null)
    {
        if (!$ruleId) return Mage::getModel('customer/customer')->getCollection();

        $rule = $this->load($ruleId);
        if (!$rule->getIsActive()) return new Mage_Customer_Model_Entity_Customer_Collection();

        $matchesArray = AW_Marketsuite_Model_Customer_Abstract::getInstance()->getMatches($rule);
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->getSelect()
            ->where('find_in_set(e.entity_id, "'.implode(',',$matchesArray).'")');

        return $collection;
    }

    /**
     * Return customers collection which satisfy MSS rule requirements
     * @param int $ruleId
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function exportOrders($ruleId = null)
    {
        if (!$ruleId) return Mage::getModel('sales/order')->getCollection();
        
        $rule = $this->load($ruleId);
        if (!$rule->getIsActive()) return new Mage_Sales_Model_Mysql4_Order_Collection();

        $matchesArray = AW_Marketsuite_Model_Order_Abstract::getInstance()->getMatches($rule);
        
        $collection = Mage::getResourceModel('sales/order_collection');
        $collection->getSelect()
            ->where('find_in_set(e.entity_id, "'.implode(',',$matchesArray).'")');

        return $collection;
    }
}