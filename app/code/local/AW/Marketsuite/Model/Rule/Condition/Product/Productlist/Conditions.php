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

class AW_Marketsuite_Model_Rule_Condition_Product_Productlist_Conditions extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_product_productlist_conditions')
            ->setValue(null);
    }

    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();
        $attr = AW_Marketsuite_Model_Attribute_Abstract::getInstance()->getAttributeByCode($attrCode);

        if ($attr && $attr->getFrontendInput() == 'multiselect') {
                $value = $object->getData($attrCode);
                $value = strlen($value) ? explode(',', $value) : array();
                return $this->validateAttribute($value);
            }
        if ($attr && $attr->getFrontendInput() == 'date') {
                $value = $object->getData($attrCode);
                $value = date('Y-m-d', strtotime($value));
                return $this->validateAttribute($value);
            }

        return Mage_Rule_Model_Condition_Abstract::validate($object);
    }
    
    public function getValueElementType() {
  
        if ($this->getAttribute() == 'category') {             
            return 'multiselect';
        }

       return parent::getValueElementType();
    }
    
    public function getValueSelectOptions() {
        
         
        if ($this->getAttribute() == 'category') {
            
            return Mage::helper('marketsuite')->getCategoriesArray();            
        }

        return parent::getValueSelectOptions();
    }
    
    
    
    
     public function getDefaultOperatorInputByType() {         
         
         $inputByType = parent::getDefaultOperatorInputByType();
                 
          $inputByType['multiselect'] = array('()', '!()');           
        
        return $inputByType;
    }
    
     public function getInputType() {
         
        switch ($this->getAttribute()) {
            
            case 'category':
                return 'multiselect';
        }

        return parent::getInputType();
    }
    
    
     public function loadAttributeOptions() {
       
         parent::loadAttributeOptions();
        
         $options = $this->getAttributeOption();         
         $options['category'] = Mage::helper('marketsuite')->__('Category');
         
         $this->setAttributeOption($options); 
          
        return $this;
    }

    
    
    
    
}