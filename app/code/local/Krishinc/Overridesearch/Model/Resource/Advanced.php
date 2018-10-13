<?php

class Krishinc_Overridesearch_Model_Resource_Advanced extends Mage_CatalogSearch_Model_Resource_Advanced 
{
	 /**
     * Prepare search condition for attribute
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @param Mage_CatalogSearch_Model_Resource_Advanced_Collection $collection
     * @return mixed
     */
    public function prepareCondition($attribute, $value, $collection)
    {
        $condition = false;

        if (is_array($value)) {
            if (!empty($value['from']) || !empty($value['to'])) { // range
                $condition = $value;
            } else if ($attribute->getBackendType() == 'varchar') { // multiselect
                $condition = array('in_set' => $value);
            } else if (!isset($value['from']) && !isset($value['to'])) { // select
                $condition = array('in' => $value);
            }
        } else {
            if (strlen($value) > 0) {
                if (in_array($attribute->getBackendType(), array('varchar', 'text', 'static'))) {
                	/*****START:: Added by bijal to search with multiple sku's*****/ 
                	if($attribute->getAttributeCode() == 'sku')
                	{
                		$condition = array('in' => explode(',',$value)); 
                	} else {
                    	$condition = array('like' => '%' . $value . '%'); // text search
                	}  
                	/*****END*****/
//                	$condition = array('like' => '%' . $value . '%'); // text search
                } else {
                    $condition = $value;
                }
            }
        }

        return $condition;
    }
}