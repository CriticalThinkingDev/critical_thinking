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

class AW_Marketsuite_Model_Rule_Condition_Order_Numberorders_Conditions extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_order_numberorders_conditions')
            ->setValue(null);
    }

    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(array(
            'order_status'  => $hlp->__('Order status'),
            'order_date' => $hlp->__('Order date'),
            'order_store_id' => $hlp->__('Store')
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

     public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'order_status':
                return 'select';
            case 'order_date':
                return 'date';
            case 'order_store_id':
                return 'multiselect';
        }
        return 'string';
    }


  public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'order_status':
                return 'select';
            case 'order_date':
                return 'date';
            case 'order_store_id':
                return 'multiselect';
        }
        return 'text';
    }


    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }
    
    public function getDefaultOperatorInputByType() {

        $inputByType = parent::getDefaultOperatorInputByType();

        $inputByType['multiselect'] = array('()', '!()');

        return $inputByType;
    } 
    
     public function loadOperatorOptions()
    {
         parent::loadOperatorOptions();
         
         $operatorbytype = $this->getOperatorByInputType();
         
         $operatorbytype['multiselect'] = array('()', '!()');
        
        $this->setOperatorByInputType($operatorbytype);
         
        return $this;
    }


    public function getValueSelectOptions()
    {
        switch ($this->getAttribute()) {
            case 'order_status':
                $options = Mage::helper('marketsuite')->getStatusesArray();
                break;
            case 'order_store_id':
                $options = Mage::helper('marketsuite')->getStoresArray();
                break;
            default:
                $options = array();
        }
        $this->setData('value_select_options', $options);
        
        return $this->getData('value_select_options');
    }

}