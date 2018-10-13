<?php
/**
 * Downloadplus Attribute Helper
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2012 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Helper_Attribute extends Mage_Core_Helper_Abstract
{

    protected $_product = null;
    
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }
    
    public function getAttributeFormHtml($type, $code, $id=null, $name=null, $label=true, $storeId=null)
    {
        $html= null;
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode($type, $code);
        if (!$storeId) {
            $storeId = Mage::helper('downloadplus')->getStoreId();
        }
        $exclude = Array();

        if ($attribute) {
            $form = new Varien_Data_Form();
            
            if ( ($inputType = $attribute->getFrontend()->getInputType())
                && !in_array($attribute->getAttributeCode(), $exclude)
                && ('media_image' != $inputType) ) {
            
                    $fieldType      = $inputType;
                    $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                    if (!empty($rendererClass)) {
                        $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                        $form->addType($fieldType, $rendererClass);
                    }
            
                    $elementId = $id?$id:$attribute->getAttributeCode();
                    $elementName = $name?$name:$attribute->getAttributeCode();
                    
                    $element = $form->addField($elementId, $fieldType,
                        array(
                            'name'      => $elementName,
                            'label'     => $label?$this->__($attribute->getFrontend()->getLabel()):null,
                            'class'     => $attribute->getFrontend()->getClass(),
                            'required'  => $attribute->getIsRequired(),
                            'note'      => $attribute->getNote(),
                            'disabled'  => ($storeId>0)
                        ))
                        ->setEntityAttribute($attribute);
            
                    if ($inputType == 'select' || $inputType == 'multiselect') {
                        $element->setValues($attribute->getSource()->getAllOptions(true, true));
                    } elseif ($inputType == 'date') {
                        $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                        $element->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                    }
                    
                    if ($this->_product) {
                        $element->setValue($this->_product->getData($attribute->getAttributeCode()));
                    }
                }

            $html = $form->toHtml();
        }
        
        return $html;    
    }

    public function getAttributeFrontendValue($code)
    {
        if (!$this->_product) {
            return null;
        }
        
        $value = $this->_product->getAttributeText($code);
        if (empty($value) ) { // use the EAV tables only if the flat table doesn't work
            $value = $this->_product->getResource()->getAttribute($code)->getFrontend()->getValue($this->_product);
        }        
        return $value;
    }
    
}
