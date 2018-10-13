<?php

class Inchoo_Productfilter_Block_Renderer_Subject extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	 
   /**
     * Render a grid cell as options
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        
        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            ///START added for category filter
	        $attr = $this->getColumn()->getIndex();
		 
			$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attr);
	
	    	$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
	    	if($attribute->getData('frontend_input') == 'multiselect'){
	    		 
	    		$value =  $row->getData($attr);
	    		 
	    		if(strstr($value,',')) {
	    			$value = explode(',',$value);
	    		}
	    		 
	    	} else {
	    	
				$value = $row->getData($attr);
	    	}
			 
	        ///END added for category filter 
            
            if (is_array($value)) {
                $res = array();
                foreach ($value as $item) {
                    if (isset($options[$item])) {
                        $res[] = $this->escapeHtml($options[$item]);
                    }
                    elseif ($showMissingOptionValues) {
                        $res[] = $this->escapeHtml($item);
                    }
                }
                return implode(', ', $res);
            } elseif (isset($options[$value])) {
                return $this->escapeHtml($options[$value]);
            } elseif (in_array($value, $options)) {
                return $this->escapeHtml($value);
            }
        }
    }
    
}