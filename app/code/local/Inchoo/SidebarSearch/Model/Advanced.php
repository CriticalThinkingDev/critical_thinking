<?php

class Inchoo_SidebarSearch_Model_Advanced extends Mage_CatalogSearch_Model_Advanced
{
	
	 
	/**
     * Add advanced search filters to product collection
     *
     * @param   array $values
     * @return  Mage_CatalogSearch_Model_Advanced
     */ 
     public function addFilters($values)
    {
    	
        $attributes     = $this->getAttributes();
        $hasConditions  = false;
        $allConditions  = array(); 
        $isNew = false;
    	if(isset($values['awd'])) {$values['award'] = array('all'); unset($values['awd']);}
    	if(array_key_exists('new',$values)) {  $isNew = true;unset($values['new']);}
	 
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (!isset($values[$attribute->getAttributeCode()])) {
                continue;
            }
            $value = $values[$attribute->getAttributeCode()];

            if ($attribute->getAttributeCode() == 'price') {
                $value['from'] = isset($value['from']) ? trim($value['from']) : '';
                $value['to'] = isset($value['to']) ? trim($value['to']) : '';
                if (is_numeric($value['from']) || is_numeric($value['to'])) {
                    if (!empty($value['currency'])) {
                        $rate = Mage::app()->getStore()->getBaseCurrency()->getRate($value['currency']);
                    } else {
                        $rate = 1;
                    }
                    if ($this->_getResource()->addRatedPriceFilter(
                        $this->getProductCollection(), $attribute, $value, $rate)
                    ) {
                        $hasConditions = true;
                        $this->_addSearchCriteria($attribute, $value);
                    }
                }
            } else if ($attribute->isIndexable() ) { 
            	/********START::: Added by bijal*****/
            	$val = '';
            	if((isset($value[0]) && $value[0] == 'all') || $value == 'all')
            	{
            		if($attribute->getAttributeCode() == 'grade') {
            			$this->_searchCriterias[] = array('name' => 'Grade', 'value' => 'All Grades (All Ages)');
            		}elseif ($attribute->getAttributeCode() == 'producttype'){
            				$this->_searchCriterias[] = array('name' => $attribute->getAttributeLabel(), 'value' => 'All Products');
            		}elseif ($attribute->getAttributeCode() == 'subject'){
            				$this->_searchCriterias[] = array('name' => 'Subject', 'value' => 'All Subjects');
            		}elseif ($attribute->getAttributeCode() == 'product_type'){
            				$this->_searchCriterias[] = array('name' => 'Products', 'value' => 'All Products');
            		} elseif ($attribute->getAttributeCode() == 'award'){
				            $this->_searchCriterias[] = array('name' => 'Awards', 'value' => 'Award Winner'); 
            		}
            		
            		$options = $attribute->getSource()->getAllOptions();  
            		$value = array();
            		foreach ($options as $option)
            		{
            			if(!empty($option['value'])) {
            				$value[]=$option['value']; 
            				$val = 'all';
            			}
            		}  
            	}
            	/****END***/
 
                if (!is_string($value) || strlen($value) != 0) {
                 
                    if ($this->_getResource()->addIndexableAttributeModifiedFilter(
                        $this->getProductCollection(), $attribute, $value)) {
                        $hasConditions = true;
                        if($val == ''){  
                      	  $this->_addSearchCriteria($attribute, $value);
                        }
                    }
                  //  echo $this->getProductCollection()->getSelect();
                }
            } else {
				if($attribute->getAttributeCode() == 'is_sale') {

                        $saleIds = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('is_sale', 1)->addAttributeToFilter('status', 1)->getAllIds();
                        $array =array();
                        foreach($saleIds as $id){
                            $_grouped_parents_ids = Mage::getModel('catalog/product_type_grouped') ->getParentIdsByChild($id);
                            foreach($_grouped_parents_ids as $gId){
                                $array[]= $gId;
                            }
                            $array[]= $id;

                        }
                        $SaleIds = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id', array('in' => $array));
                        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($SaleIds);
                        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($SaleIds);
                        $this->getProductCollection()
                            ->addAttributeToFilter('entity_id', array('in' =>  $SaleIds->getAllIds()));
                           continue;
                }			
            	
                $condition = $this->_prepareCondition($attribute, $value);
              
                if ($condition === false) {
                    continue;
                }

                $this->_addSearchCriteria($attribute, $value);

                $table = $attribute->getBackend()->getTable();
                if ($attribute->getBackendType() == 'static'){
                    $attributeId = $attribute->getAttributeCode();
                } else {
                    $attributeId = $attribute->getId();
                }
                $allConditions[$table][$attributeId] = $condition;
            }
        }
        if (array_key_exists('sku',$values))
        {
        	if(strstr($values['sku'],','))
        	{
        		$total = sizeof($this->_searchCriterias);
        		if($total > 0)
        		{
        			$i=0;
        			for($i=0;$i<$total;$i++)
        			{
        				if($this->_searchCriterias[$i]['name'] == 'SKU')
        				{ 
        					$this->_searchCriterias[$i]['value'] = 'Products';
        				}
        			}
        		} 
        	}
        }
     // print_r($allConditions);exit;
       
        if ($allConditions) {
			$qry = $this->getProductCollection()->addFieldsToFilter($allConditions);
	
        } /*else if (!$hasConditions) {
            Mage::throwException(Mage::helper('catalogsearch')->__('Please specify at least one search term.'));
        }*/
         if($isNew)
        { 
    	   $todayStartOfDayDate  = Mage::app()->getLocale()->date()
	            ->setTime('00:00:00')
	            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	
	        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
	            ->setTime('23:59:59')
	            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        	
            /*$this->getProductCollection()->addAttributeToFilter('news_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
              );
//            ->addAttributeToSort('news_from_date', 'desc');*/

$newItemIds = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('news_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
                ->addAttributeToFilter('news_to_date', array('or'=> array(
                    0 => array('date' => true, 'from' => $todayStartOfDayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                        array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
                )->addAttributeToFilter('status', 1)->getAllIds();
//            ->addAttributeToSort('news_from_date', 'desc');



             $array =array();
             foreach($newItemIds as $id){
                 $_grouped_parents_ids = Mage::getModel('catalog/product_type_grouped') ->getParentIdsByChild($id);
                 foreach($_grouped_parents_ids as $gId){
                     $array[]= $gId;
                 }
                 $array[]= $id;

             }
             $newItemIds = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id', array('in' => $array));
             Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($newItemIds);
             Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($newItemIds);
             $this->getProductCollection()
                 ->addAttributeToFilter('entity_id', array('in' =>  $newItemIds->getAllIds()));
            $this->_searchCriterias[] = array('name' => 'New', 'value' => 'New');
        }
		
		
		 $typeArray ='';
        if(isset($values['product_type'])){
            $typeArray = $values['product_type'];
        }



        if($typeArray){
            $newIds = array();
            $allIds = $this->getProductCollection()->getAllIds();
	    if($typeArray['0']=='all'){
                $newIds =  $allIds;
            }else{	
            $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('entity_id', array('in' => $allIds));

            foreach($products as $product){
                if($product->getTypeId()=='grouped'){
                    //if($product->getId()=='1383'){

                    if(in_array($product->getProductType(),$typeArray)){
                        $newIds[] = $product->getId();
                    }else{
                        $associatedProIs =  $product->getTypeInstance(true)->getAssociatedProductIds($product);
                        $acoll = Mage::getModel('catalog/product')->getCollection()
                                       ->addAttributeToSelect('*')
                                       ->addAttributeToFilter('entity_id', array('in' => $associatedProIs))
                            ->addFieldToFilter('product_type',array('in' => $typeArray))
                            ->addAttributeToFilter('status', '1')
                        ;
                        if(count($acoll)){
                            $newIds[] = $product->getId();
                        }
                    }
                   // }
                }else{
                    $newIds[] = $product->getId();
                }
            }
}
			$this->getProductCollection()
            ->addAttributeToFilter('entity_id', array('in' => $newIds)); 
        }
        // $grade = $values['grade']['0'];
        //$subject = $values['subject']['0'];
        //$typeP =$typeArray['0'];

      $grade = '';
        if(isset($values['grade']['0'])){
            $grade = $values['grade']['0'];
        }
        $subject = '';
        if(isset($values['subject']['0'])){
            $subject = $values['subject']['0'];
        }
        $typeP  = '';
        if(isset($typeArray['0'])){
            $typeP =$typeArray['0'];
        }
        if ((empty($typeP) || $typeP=='all') && (empty($grade) || $grade=='all') && $subject!='' && $subject!='all'){

            $newIds = array();
            $allIds = $this->getProductCollection()->getAllIds();
            $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')
                                ->addAttributeToFilter('entity_id', array('in' => $allIds))
                                ->addAttributeToFilter('attribute_set_id', 11);
            $newIds = $products->getAllIds();
            $this->getProductCollection()
                    ->addAttributeToFilter('entity_id', array('in' => $newIds));
        }else{
            $this->getProductCollection()->addAttributeToFilter('attribute_set_id', array('neq' => 11));
        }
 if($typeP!=125){
          // $this->getProductCollection()->addAttributeToFilter('product_type', array('null' => true));
           //$this->getProductCollection()->addAttributeToFilter('product_type', array('neq' => 125));
           $this->getProductCollection()->addAttributeToFilter(
               array(
                   array('attribute'=> 'product_type',array('null' => true)),
                   array('attribute'=> 'product_type',array('neq' => 125)),

               )
           );

       }
        
        return $this;
    }

    
}
