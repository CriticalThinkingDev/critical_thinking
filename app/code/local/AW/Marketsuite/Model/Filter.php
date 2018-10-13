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


class AW_Marketsuite_Model_Filter extends Mage_Rule_Model_Rule {

    protected static $instance;

    public function _construct() {
        parent::_construct();
        $this->_init('marketsuite/filter');
    }

    public function getConditionsInstance() {
        return Mage::getModel('marketsuite/rule_condition_combine');
    }

    /**
     * Return all MSS rules as collection
     * @return AW_Marketsuite_Model_Mysql4_Filters_Collection
     */
    public function getRuleCollection() {
        return $this->getCollection();
    }

    public static function getInstance() {
        
        if (!self::$instance) {
            $magentoVersion = Mage::getVersion();
            $preffix = 'mag13';
            if (preg_match('/^(1.1[0-9]|1.9|1.8|1.4|1.5|1.6|1.7)/', $magentoVersion))
                $preffix = 'mag18';
            if (preg_match('/^1.4.0/', $magentoVersion))
                $preffix = 'mag13';
              
            self::$instance = Mage::getConfig()->getModelClassName('marketsuite/filter_' . $preffix);
        
        }
        
        return new self::$instance;
    }

    /**
     * Check $object via MSS rule
     * @param Mage_Sales_Model_Order | Mage_Customer_Model_Customer | Mage_Sales_Model_Quote $object
     * @param int $ruleId
     * @return boolean
     */
    public function checkRule($object, $ruleId) {
        return self::getInstance()->checkRule($object, $ruleId);
    }

    /**
     * Return customers collection which satisfy MSS rule requirements
     * @param int $ruleId
     * @return Mage_Customer_Model_Entity_Customer_Collection
     */
    public function exportCustomers($ruleId = null) {
        return self::getInstance()->exportCustomers($ruleId);
    }

    /**
     * Return customers collection which satisfy MSS rule requirements
     * @param int $ruleId
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function exportOrders($ruleId = null) {
        return self::getInstance()->exportOrders($ruleId);
    }

}