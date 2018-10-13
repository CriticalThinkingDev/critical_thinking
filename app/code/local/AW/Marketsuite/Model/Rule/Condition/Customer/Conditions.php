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

class AW_Marketsuite_Model_Rule_Condition_Customer_Conditions extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */

    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_customer_conditions')
            ->setValue(null);
    }

    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(array(
            'group_id' => $hlp->__('Customer Group'),
            'dob' => $hlp->__('Date of Birth'),
            'billing_address' => $hlp->__('Billing address'),
            'shipping_address' => $hlp->__('Shipping address'),
            'email' => $hlp->__('Email'),
            'gender' => $hlp->__('Gender'),
            'firstname' => $hlp->__('First name'),
            'lastname' => $hlp->__('Last name'),
            'newslettersubscription' => $hlp->__('Newsletter subscription'),
            'annewslettersubscription' => $hlp->__('Advanced newsletter subscription'),
        ));
        return $this;
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'annewslettersubscription':
            case 'newslettersubscription':
            case 'gender':
                return 'select';

            case 'dob':
                return 'date';

            default:
                return 'string';

        }
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

    public function getValueElement()
    {
        $element = parent::getValueElement();
        switch ($this->getInputType()) {
            case 'date':
                $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                break;
        }
        return $element;
    }

    public function getExplicitApply()
    {
        switch ($this->getInputType()) {
            case 'date':
                return true;
        }
        return false;
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'annewslettersubscription':
            case 'newslettersubscription':
            case 'group_id':
            case 'gender':
                return 'select';

            case 'dob':
                return 'date';

            default:
                return 'text';
        }
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'annewslettersubscription':
                    $options = Mage::getModel('advancednewsletter/segmentsmanagment')
                        ->getSegmentList();
                    break;

                case 'newslettersubscription':
                    $options = array(
                        array('value' => '0', 'label' => 'No'),
                        array('value' => '1', 'label' => 'Yes')
                    );
                    break;

                case 'gender':
                    $options = Mage::getModel('marketsuite/source_gender')->toOptionArray();
                    break;

                case 'group_id':
                    $options = Mage::getResourceModel('customer/group_collection')
                        ->addFieldToFilter('customer_group_id', array('gt' => 0))
                        ->load()
                        ->toOptionHash();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function asHtml()
    {
        if ($this->getAttribute() == 'annewslettersubscription') {
            $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
            if (!in_array('AW_Advancednewsletter', $modules)) {
                $html = '<a href="http://ecommerce.aheadworks.com/extensions/advanced-newsletter.html">Advanced Newsletter extension</a> is required for targeted newsletter functionality.';
                $html .= $this->getRemoveLinkHtml();
                return $html;
            }
        }
        return parent::asHtml();
    }

    public function validate(Varien_Object $object)
    {
        if ($object instanceof AW_Marketsuite_Model_Customer_Abstract) {
            $customer = $object;
        }
        else {
            $customer = $object->getCustomer();
        }
        if ($this->getAttribute() == 'newslettersubscription' || $this->getAttribute() == 'annewslettersubscription')
            $customer->addSubscriptionInfo();
        return parent::validate($customer);
    }

    public function validateAttribute($validatedValue)
    {
        if ($this->getValueParsed() == AW_Marketsuite_Model_Source_Gender::NOT_SPECIFIED
            && $validatedValue === null
        ) {
            return true;
        }
        return parent::validateAttribute($validatedValue);
    }
}
