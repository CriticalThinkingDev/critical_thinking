<?php


class Krishinc_Grouped_Block_Product_View_Attributes extends Mage_Catalog_Block_Product_View_Attributes
{
   
     
    public function getAssociatedProducts()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->getAssociatedProducts($this->getProduct());
    }
  	/**
     * Set preconfigured values to grouped associated products
     *
     * @return Mage_Catalog_Block_Product_View_Type_Grouped
     */
    public function setPreconfiguredValue() {
        $configValues = $this->getProduct()->getPreconfiguredValues()->getSuperGroup();
        if (is_array($configValues)) {
            $associatedProducts = $this->getAssociatedProducts();
            foreach ($associatedProducts as $item) {
                if (isset($configValues[$item->getId()])) {
                    $item->setQty($configValues[$item->getId()]);
                }
            }
        }
        return $this;
    }
    
    
     /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     */
    public function getAdditionalData(array $excludeAttr = array())
    {
        $data = array();
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
//            if ($attribute->getIsVisibleOnFront() && $attribute->getIsUserDefined() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                	continue;
                    $value = Mage::helper('catalog')->__('N/A');
                } elseif ((string)$value == '') {
                    continue;
                	$value = Mage::helper('catalog')->__('No');
                 } elseif ((string)$value == 'No') {
                    continue;
                	$value = Mage::helper('catalog')->__('No');
                    
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) { 
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                    
                }elseif (($attribute->getAttributeCode() == 'award') || ($attribute->getAttributeCode()== 'grade')|| ($attribute->getAttributeCode()== 'playdemo_url')|| ($attribute->getAttributeCode()== 'available_text')|| ($attribute->getAttributeCode()== 'is_sale'||  $attribute->getAttributeCode()== 'is_breakout_bundles'||  $attribute->getAttributeCode()== 'core_curriculum'))
                {
                	continue;
                }

                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $attribute->getAttributeCode()
                    );
                }
            }
        }
        return $data;
    }
}
