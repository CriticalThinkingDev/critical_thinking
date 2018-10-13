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

class AW_Marketsuite_Model_Rule_Condition_Product_Productlist extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    
    protected $_productResouce = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_product_productlist')
            ->setValue(null);
      
    }


    public function getNewChildSelectOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute)
        {
            if (preg_match('/^(1.8|1.9|1.1[0-9]|1.4.1|1.4.2|1.5|1.6|1.7)/', Mage::getVersion()))
            { 
                if (!$attribute->isAllowedForRuleCondition() || !$attribute->getIsUsedForPromoRules())
                    continue;
            }
            else
            {
                if (!$attribute->isAllowedForRuleCondition() || !$attribute->getIsUsedForPriceRules())
                    continue;
            }
            if (Mage::helper('marketsuite')->CheckUselessProductAttributes($attribute->getFrontendLabel())) continue;

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $leftattributes = array();
        foreach ($attributes as $code=>$label)
        {
            $leftattributes[] = array('value'=>'marketsuite/rule_condition_product_productlist_conditions|'.$code, 'label'=>$label);
        }

        $conditions = Mage_Rule_Model_Condition_Combine::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, $leftattributes);
        
        
        /* Add category as attribute to product attributes */
        
        $conditions[] = array(
            
            'value'=>'marketsuite/rule_condition_product_productlist_conditions|category', 
            'label'=> Mage::helper('marketsuite')->__('Category')
            
        );
        
        /* */ 
       
        return $conditions;
    }

    public function loadArray($arr, $key='conditions')
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
            'product'  => $hlp->__('Product'),
        ));
        return $this;
    }

    public function loadValueOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setValueOption(array(
            'wishlist'  => $hlp->__('Wishlist'),
            'shoppingcart'  => $hlp->__('Shopping cart'),
        ));
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('marketsuite')->__('is'),
            '!='   => Mage::helper('marketsuite')->__('is not'),
        ));
        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('salesrule')->__("%s %s in the %s with %s of these conditions match:",
            $this->getAttributeElement()->getHtml(),
            $this->getOperatorElement()->getHtml(),
            $this->getValueElement()->getHtml(),
            $this->getAggregatorElement()->getHtml()
        );
        if ($this->getId()!='1')
        {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }
    
    public function getProductResouce() {
        
        if(null === $this->_productResouce) {            
            
            $this->_productResouce = Mage::getResourceModel('catalog/product');
        }
        
        return $this->_productResouce;
     
    }

    public function  validate(Varien_Object $object)
    {
        if ($object instanceof AW_Marketsuite_Model_Order_Abstract)
            $object = $object->getCustomer();

        if ($this->getValue()=='shoppingcart')
        {
            if ($object instanceof AW_Marketsuite_Model_Customer_Abstract)
                $quote = $object->getCart();
            elseif ($object instanceof AW_Marketsuite_Model_Cart_Abstract)
                $quote = $object;
            else return false;
            
            foreach ($quote->getProducts() as $product) {
                
                /* Get ids of categories related to product */
                $product->setId($product->getEntityId());                
                $product->setCategory($this->getProductResouce()->getCategoryIds($product));
                /* category ids */
                
                if (parent::validate($product)) {
                    return $this->validateAttribute($this->getValue());
                }
            }
                
            return !$this->validateAttribute($this->getValue());
        }

        if ($this->getValue()=='wishlist')
        {
            if ($object instanceof AW_Marketsuite_Model_Cart_Abstract)
                $object = $object->getCustomer();
            $wishlist = $object->getWishlist();

            foreach ($wishlist->getProducts() as $product) {
                
                /* Get ids of categories related to product */
                $product->setId($product->getEntityId());                
                $product->setCategory($this->getProductResouce()->getCategoryIds($product));
                /* category ids */
                
                if (parent::validate($product)) {
                    return $this->validateAttribute($this->getValue());
                }
            }

            return !$this->validateAttribute($this->getValue());
        }

        return false;
    }
}