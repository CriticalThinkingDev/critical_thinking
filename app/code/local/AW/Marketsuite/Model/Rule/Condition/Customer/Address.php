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

class AW_Marketsuite_Model_Rule_Condition_Customer_Address extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_customer_address')
            ->setValue(null);
    }

    
    public function getNewChildSelectOptions()
    {
        $conditions = Mage_Rule_Model_Condition_Combine::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'marketsuite/rule_condition_customer_address_conditions|city', 'label'=>Mage::helper('salesrule')->__('City')),
            array('value'=>'marketsuite/rule_condition_customer_address_conditions|region', 'label'=>Mage::helper('salesrule')->__('State')),
            array('value'=>'marketsuite/rule_condition_customer_address_conditions|country_id', 'label'=>Mage::helper('salesrule')->__('Country')),
            array('value'=>'marketsuite/rule_condition_customer_address_conditions|telephone', 'label'=>Mage::helper('salesrule')->__('Telephone')),
            array('value'=>'marketsuite/rule_condition_customer_address_conditions|postcode', 'label'=>Mage::helper('salesrule')->__('Zip')),
            array('value'=>'marketsuite/rule_condition_customer_address_conditions|company', 'label'=>Mage::helper('salesrule')->__('Company')),
        ));
        return $conditions;
    }

    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(array(
            'billing' => $hlp->__('Billing'),
            'shipping' => $hlp->__('Shipping'),
        ));
        return $this;
    }

    public function loadArray($arr, $key='conditions')
    {
        $this->setAttribute($arr['attribute']);
        //$this->setOperator($arr['operator']);

        parent::loadArray($arr, $key);
        return $this;
    }

    /*public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(array(
            'billing' => $hlp->__('Billing'),
            'shipping' => $hlp->__('Shipping'),
        ));
        return $this;
    }*/

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('rule')->__('is'),
            '!='  => Mage::helper('rule')->__('is not'),
        ));
        return $this;
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array(
            1=>'is',
            0=>'is not',
        ));
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('salesrule')->__("Customer %s address %s matched %s",
              $this->getAttributeElement()->getHtml(),
              $this->getValueElement()->getHtml(),
              $this->getAggregatorElement()->getHtml()
           );
           if ($this->getId()!='1') {
               $html.= $this->getRemoveLinkHtml();
           }

        return $html;
    }

    protected function getAddress($object)
    {
        $address = null;
        if ($this->getAttribute()=='billing')
            $address = $object->getAddress(AW_Marketsuite_Model_Customer_Abstract::DEFAULT_BILLING_ADDRESS);
        if ($this->getAttribute()=='shipping')
            $address = $object->getAddress(AW_Marketsuite_Model_Customer_Abstract::DEFAULT_SHIPPING_ADDRESS);

        return $address;
    }

    public function validate(Varien_Object $object)
    {
        if ($object instanceof AW_Marketsuite_Model_Customer_Abstract)
            $address = $this->getAddress($object);
        else 
            $address = $this->getAddress($object->getCustomer());

        return parent::validate($address);
    }
}