<?php

class Inchoo_Productfilter_Block_Renderer_Grade extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	 
    public function render(Varien_Object $row) {
    	
    	 $options = $this->getColumn()->getOptions();
        
        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        $value = $row->getData($this->getColumn()->getIndex()); 
        $attr = $this->getColumn()->getIndex(); 
		$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attr);
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        if (!empty($options)) {
        	//var_dump($options);
	       if(is_array($options)) { 
	       	 
		       	if(strpos($value,',') !== FALSE) {
			    	 
			    	if($attribute->getData('frontend_input') == 'multiselect'){ 
			    		$value =  $row->getData($attr);
			    		return 	$value = $this->getProductGrade($value,$options);
		 
			    	}
		       	} else { 
		       		 $gradeLabels =  $this->getGradeLabels($options);
                     if(isset($gradeLabels[(int)$value])){
                         return $gradeLabels[(int)$value];
                     }else {
                         return '';   
                     }
		       		  
		       	}
	    	}   
        }
    	
      
    }
 
/**
	 * This function is used to get Custom grade values as a range
	 *
	 * @param array $_grades
	 * @return string grade label
	 */
	public function getProductGrade($_grades,$options)
	{
		$arrProductGrades = explode(',',$_grades);
		 
		$arrProductGrades1 = array();
		$arrProductGrades1 = array_reverse($arrProductGrades); 
		$arrGrades = $this->getGradeLabels($options);  
		$cntProGrades = sizeof($arrProductGrades); 
		$last = $arrProductGrades[$cntProGrades-1];
		if($cntProGrades > 1): 
			$label = $arrGrades[$arrProductGrades[0]] .' - '. $arrGrades[$last];
		else:
			$label = $arrGrades[0]; 
		endif;
		 
		return $label;
		
	}
	
	/**
	 * Create custom function to get custom labels of grade
	 *
	 * @return Grade Array
	 */
	public function getGradeLabels($grades)
	{ 
		// print_r($grades);
		$arrCustomGradeLabels = array();
		$i=0;
		$j=1;
		$count = sizeof($grades)-1;
		foreach ($grades as $key => $value) { 
			
			if($i == 0):
				$arrCustomGradeLabels[$key] = 'Toddler';
			elseif ($i == 1):
				$arrCustomGradeLabels[$key] = 'PreK';
			elseif ($i == 2):
				$arrCustomGradeLabels[$key] = 'K';
			else:
				if($i == $count): 
				$j--;
				$j = $j.'+';
				endif;
				$arrCustomGradeLabels[$key] = $j;
				$j++;
			endif;
			$i++;
		} 
		return $arrCustomGradeLabels;
	}
	 
    
}