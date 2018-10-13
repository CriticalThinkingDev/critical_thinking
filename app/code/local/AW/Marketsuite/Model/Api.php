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

class AW_Marketsuite_Model_Api
{
    /**
     * Check $object via MSS rule
     * @param Mage_Sales_Model_Order | Mage_Customer_Model_Customer | Mage_Sales_Model_Quote $object
     * @param int $ruleId
     * @return boolean
     */
    public function checkRule($object, $ruleId)
    {
        return Mage::getModel('marketsuite/filter')->checkRule($object, $ruleId);
    }

    /**
     * Return customers collection which satisfy MSS rule requirements
     * @param int $ruleId
     * @return Mage_Customer_Model_Entity_Customer_Collection
     */
    public function exportCustomers($ruleId = null)
    {
        return Mage::getModel('marketsuite/filter')->exportCustomers($ruleId);
    }

    /**
     * Return customers collection which satisfy MSS rule requirements
     * @param int $ruleId
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function exportOrders($ruleId = null)
    {
       return Mage::getModel('marketsuite/filter')->exportOrders($ruleId);
    }

    /**
     * Return all MSS rules as collection
     * @return AW_Marketsuite_Model_Mysql4_Filters_Collection
     */
    public function getRuleCollection()
    {
        return Mage::getModel('marketsuite/filter')->getCollection();
    }
}