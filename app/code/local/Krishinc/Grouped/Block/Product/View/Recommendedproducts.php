<?php


class Krishinc_Grouped_Block_Product_View_Recommendedproducts extends Mage_Catalog_Block_Product_Abstract
{
    protected $_product = null;

    function getProduct()
    { 
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
    
    public function getItems()
    {
    	if($_itemCollection = $this->recentlyViewedProducts())
    	{
    		return $_itemCollection;
    	} else {
    		return $this->bestsellerProducts();
    	}
    }
    public function recentlyViewedProducts()
    {
    	$indexModel = Mage::getModel('reports/product_index_viewed');
    	$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
    	 
		$product = $this->getProduct();
        $collection = $indexModel
		                ->getCollection()
		                ->addAttributeToSelect($attributes)
		                ->addAttributeToSelect('series');
		 

        if ($this->getCustomerId()) {
            $collection->setCustomerId($this->getCustomerId());
        }
		
 
         
        $collection->excludeProductIds($indexModel->getExcludeProductIds())
            ->addUrlRewrite()
            ->addFieldToFilter('entity_id',array('neq' =>$product->getId()))
            ->setPageSize($this->getPageSize())
            ->setCurPage(1);

            /* Price data is added to consider item stock status using price index */
            $collection->addPriceData();

            $ids = $this->getProductIds();
            if (empty($ids)) {
                $collection->addIndexFilter();
            } else {
                $collection->addFilterByIds($ids);
            }
            $collection->setAddedAtOrder();
			
			$collection->getSelect()->where(" ((`e`.`is_master_group_product` = 0) OR (`e`.`is_master_group_product` IS NULL)) ");
			
            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($collection);    
            //$collection->addAttributeToFilter('series',array('neq' => 1));
            $newcollection = $collection;
	  		
        	if($product->getSubject())
        	{
    			if(strstr($product->getSubject(),','))
        		{
        			$subject = explode(',',$product->getSubject());
        			$i = 0;
	        		foreach ($subject as $sub) {
		        		$allowedSubFilters[0][$i] = 
		        			 array(
							    "finset" => array($sub)
							  );
		        		$i++;
	        		}
        		}
        		 else {
        				$allowedSubFilters[0][0] = 
		        			 array(
							    "finset" => array($product->getSubject())
							  );
        		}
        		$newcollection->addAttributeToFilter('subject',$allowedSubFilters); 
        	}
        	if($product->getGrade())
        	{ $allowedGradeFilters = array();
        		if(strstr($product->getGrade(),','))
        		{
        			$grades = explode(',',$product->getGrade());
        			$i = 0;
	        		foreach ($grades as $grade) {
		        		$allowedGradeFilters[0][$i] = 
		        			 array(
							    "finset" => array($grade)
							  );
		        		$i++;
	        		}
        		} else {
        				$allowedGradeFilters[0][0] = 
		        			 array(
							    "finset" => array($product->getGrade())
							  );
        		}
        		
        		$newcollection->addAttributeToFilter('grade',$allowedGradeFilters); 
        	} 
          
            if($newcollection->getSize() > 0) {
            	
            	return $newcollection;
            	
            } else {
            	
	        	$allowedGradeFilters = array();
        		if(strstr($product->getGrade(),','))
        		{
        			$grades = explode(',',$product->getGrade());
        			$i = 0;
	        		foreach ($grades as $grade) {
		        		$allowedGradeFilters[0][$i] =  
		        			 array(
							    "finset" => array($grade)
							  );
		        		$i++;
	        		}
        		} else {
        				$allowedGradeFilters[0][0] = 
		        			 array(
							    "finset" => array($product->getGrade())
							  );
        		}
        		$collection->addAttributeToFilter('grade',$allowedGradeFilters);
	        	
	        	if($collection->getSize() > 0)
	        	{
	        		return $collection;
	        	}
	       }
	       return false; 
    }
    
    public function bestsellerProducts()
    {
    	 
		$storeId = Mage::app()->getStore()->getId();
		$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
		$product = $this->getProduct();
		$products = Mage::getResourceModel('reports/product_collection')
					->addOrderedQty()
					->addAttributeToSelect($attributes)
				    ->addFieldToFilter('entity_id',array('neq' =>$product->getId()));  
		$products = $products->setStoreId($storeId)
					->addStoreFilter($storeId)
					->setOrder('ordered_qty', 'desc');
		
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		
		$products->getSelect()->where(" ((`e`.`is_master_group_product` = 0) OR (`e`.`is_master_group_product` IS NULL)) ");
		
		/*******START:: Logic to filter collection with current product subject and grade***/
	    $newcollection = $products;

    	if($product->getSubject())
    	{
    		if(strstr($product->getSubject(),','))
        		{
        			$subject = explode(',',$product->getSubject());
        			$i = 0;
	        		foreach ($subject as $sub) {
		        		$allowedSubFilters[0][$i] = 
		        			 array(
							    "finset" => array($sub)
							  );
		        		$i++;
	        		}
        		}
        		 else {
        				$allowedSubFilters[0][0] = 
		        			 array(
							    "finset" => array($product->getSubject())
							  );
        		}
        		$newcollection->addAttributeToFilter('subject',$allowedSubFilters); 
    	}
    	if($product->getGrade())
    	{
    		$allowedGradeFilters = array();
        		if(strstr($product->getGrade(),','))
        		{
        			$grades = explode(',',$product->getGrade());
        			$i = 0;
	        		foreach ($grades as $grade) {
		        		$allowedGradeFilters[0][$i] = 
		        			 array(
							    "finset" => array($grade)
							  );
		        		$i++;
	        		}
        		} else {
        				$allowedGradeFilters[0][0] = 
		        			 array(
							    "finset" => array($product->getGrade())
							  );
        		}
        		
        		$newcollection->addAttributeToFilter('grade',$allowedGradeFilters); 
    	}
    
     
        if($newcollection->getSize() > 0) {
        	
        	return $newcollection;
        	
        } else {
        	
        	if($product->getGrade())
        	{
        		$allowedGradeFilters = array();
        		if(strstr($product->getGrade(),','))
        		{
        			$grades = explode(',',$product->getGrade());
        			$i = 0;
	        		foreach ($grades as $grade) {
		        		$allowedGradeFilters[0][$i] = 
		        			 array(
							    "finset" => array($grade)
							  );
		        		$i++;
	        		}
        		} else {
        				$allowedGradeFilters[0][0] = 
		        			 array(
							    "finset" => array($product->getGrade())
							  );
        		}
        		
        		$products->addAttributeToFilter('grade',$allowedGradeFilters); 
        	}
        	
        	if($products->getSize() > 0)
        	{
        		return $products;
        	} 
       }
       /*******END*****/
	
		//return $products;	   
	 
    }
}
