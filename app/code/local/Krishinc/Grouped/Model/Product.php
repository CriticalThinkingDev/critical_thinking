<?php
class Krishinc_Grouped_Model_Product extends Krishinc_Customlinks_Model_Product
{
	/**
	 * This function is used to get Custom grade values as a range
	 *
	 * @param array $_grades
	 * @return string grade label
	 */
	public function getProductGrade($_grades)
	{
		$arrProductGrades = explode(',',$_grades);
		$arrProductGrades1 = array();
		$arrProductGrades1 = array_reverse($arrProductGrades); 
		$arrGrades = $this->getGradeLabels(); 
		$cntProGrades = sizeof($arrProductGrades); 
		$last = $arrProductGrades[$cntProGrades-1];
		if($cntProGrades > 1): 
			$label = $arrGrades[$arrProductGrades[0]] .'-'. $arrGrades[$last];
		else:
			$label = $arrGrades[$_grades]; 
		endif;
		return $label;
		
	}
	
	/**
	 * Create custom function to get custom labels of grade
	 *
	 * @return Grade Array
	 */
	public function getGradeLabels()
	{ 
		$grades = $this->getAllGrades();
		$arrCustomGradeLabels = array();
		$i=0;
		$j=1;

		$count = sizeof($grades)-1;
		foreach ($grades as $key => $value) { 
			
			if($i == 0):
				$arrCustomGradeLabels[$value['value']] = 'Toddler';
			elseif ($i == 1):
				$arrCustomGradeLabels[$value['value']] = 'PreK';
			elseif ($i == 2):
				$arrCustomGradeLabels[$value['value']] = 'K';
			else:
				if($i == $count):
				$j--;
				$j = $j.'+';
				endif;
				$arrCustomGradeLabels[$value['value']] = $j;
				$j++;
			endif;
			$i++;
		}
		 
	 
		return $arrCustomGradeLabels;
	}
	/**
	 * Function to get all option values of grades
	 *
	 * @return grade array with option values
	 */
	public function getAllGrades()
    {
    	$product = Mage::getModel('catalog/product');        
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('attribute_code', 'grade');      
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());       
        $grades = $attribute->getSource()->getAllOptions(false);    
        return $grades;
    }
    
    /**
	 * Function to get all option values of grades
	 *
	 * @return grade array with option values
	 */
	public function getAttributeOptionValues($attribute_code)
    {
    	$product = Mage::getModel('catalog/product');        
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('attribute_code', $attribute_code);      
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());       
        $attributeOptionValues = $attribute->getSource()->getAllOptions(false);    
        return $attributeOptionValues;
    }

 	/**
	 * This function is used to get Custom subject values with comma separator
	 * DT: 10-April-2013
	 * @param array $_subjects
	 * @return string subject label
	 */
    public function getProductSubject($_subjects)
    {
    	$subjectStr = '';
    	$_subjectArr = explode(',',$_subjects);
    	$product = Mage::getModel('catalog/product');        
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('attribute_code', 'subject');      
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());       
        $subjects = $attribute->getSource()->getAllOptions(false);   
        $totSub = sizeof($_subjectArr);
        $i = 0;
        foreach ($subjects as $key => $value) {
        	
         	if(in_array($value['value'],$_subjectArr))
         	{
         		$i++;
         		$subjectStr .= $value['label'];
         		if($i != $totSub)
         		{
         			$subjectStr .=', ';
         		}
         	}
         } 
    	return $subjectStr;
    }


}