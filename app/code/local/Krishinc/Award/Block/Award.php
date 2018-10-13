<?php

class Krishinc_Award_Block_Award extends Mage_Core_Block_Template
{
	/**
	 * Function to get product collection
	 *
	 * @return object
	 */
	
	public function getAwardProducts()
	{
		    $products = Mage::getModel('catalog/product')->getCollection();
		    $products->addAttributeToSelect(array('name','product_url','award'));
		    $products->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
		    $products->addAttributeToFilter('type_id', array('eq' => Mage_Catalog_Model_Product_Type::TYPE_GROUPED));
		    $products->addAttributeToFilter('series', array('eq' => '1'));
		
		    $visibility = array(
		        Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
		        Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
		     );
		     $products->addAttributeToFilter('visibility', $visibility);
		     $products->addAttributeToFilter('award',  array( 'notnull' => true ));
		     $products->load(); 
		    return $products;
	} 
	/**
	 * *Function to get  award name
	 *
	 * @param string $awardIds
	 * @return string
	 */
	public  function getAwardNames($awardIds)
	{
		$arrAwardIds = array();
		$arrAwardIds = explode(',',$awardIds);
		$awardCollection = Mage::getModel('award/award')->getCollection()
					->addFieldToFilter('award_id',$arrAwardIds);
		$awardNames = '';
		$cnt = sizeof($arrAwardIds);
		$i=1;
		foreach ($awardCollection as $award)
		{
			$awardNames .=	$award->getName();
			if($i < $cnt)
			{
				$awardNames .= ', ';
				$i++;
			} 
			
		}
		return $awardNames;
					
	} 
	/**
	 * Function to get all awards collection
	 *
	 * @return object
	 */
	
	public  function getAllAwards()
	{ 
		$awardCollection = Mage::getModel('award/award')->getCollection()->setOrder('awarddate','DESC'); 
		return $awardCollection;					
	}  
	
	/**
	 * Function to get award collection with company fielter
	 *
	 * @return object
	 */
	
	public  function getAllCompanyAwards()
	{ 
		$awardCollection = Mage::getModel('award/award')->getCollection()->addFieldToFilter('is_companyaward','1')->setOrder('awarddate','DESC'); 
		return $awardCollection;
					
	}
	
	/**
	 * Function to get Product Array with award detail(s) 
	 *
	 * @return array
	 */
	public function getAwardProductArray()
	{
		$awardArray = array();
		
		$awardDates = $this->getAllAwardsDates();
		$awardDates = $this->getArrayValuesRecursive($awardDates); 
		 
		$productCollection = $this->getAwardProducts();
		$i = 0;
		foreach ($productCollection as $product)
		{	
			$award = '';
			$award = $product->getAward();
			if(strstr($award,',')) { 
				$arrAwardIds = explode(',',$award);
				$j = 0;

				$awrdDate = '';
				foreach ($arrAwardIds as $value) {
					
					//if($j == 0) { 
						$awrdDate = $awardDates[$value]; 
					//} 
					//$j++; 
					
					$awardArray[$i]['product_name'] = $product->getName();
					$awardArray[$i]['product_url'] = $product->getProductUrl();
					$awardArray[$i]['award_id'] = $value;
					$awardArray[$i]['award_name'] = $this->getAwardNames($value);
					$awardArray[$i]['award_date'] = $awrdDate;
					$i++;
				}
				
			} else {
				/*if(!empty($awardDates[$product->getAward()]))
				{
					$awrdDate = $awardDates[$product->getAward()];
				}*/
				$awardArray[$i]['product_name'] = $product->getName();
				$awardArray[$i]['product_url'] = $product->getProductUrl();
				$awardArray[$i]['award_id'] = $product->getAward();
				$awardArray[$i]['award_name'] = $this->getAwardNames($product->getAward());
				$awardArray[$i]['award_date'] =  $awardDates[$product->getAward()]; 
			}
			$i++;
		}
		
		return $awardArray;
		
	}
	/**
	 * Function to get company award array
	 *
	 * @return array
	 */
	
	public function getCompanyAwardArray()
	{
		$cAwardArray = array();
		$cAwardCollection = $this->getAllCompanyAwards();
		$i =0;
		foreach ($cAwardCollection as $awd) {
			$cAwardArray[$i]['product_name'] = '';
			$cAwardArray[$i]['product_url'] = '';
			$cAwardArray[$i]['award_id'] = $awd->getAwardId();
			$cAwardArray[$i]['award_name'] = $awd->getName();
			$cAwardArray[$i]['award_date'] = $awd->getAwarddate();
			$i++;
		}
		
		return $cAwardArray;
	} 
	
	/**
	 * Function to add recursive to create array of award_id as key and value as awarddate
	 * Used in function getAwardProductArray
	 * @param array $array
	 * @return array
	 */
	public function getArrayValuesRecursive($array)
	{
	    $arrayValues = array();
	
	    foreach ($array as  $value)
	    {
	    	$arrayValues[$value['award_id']] = $value['awarddate']; 
	    }
	
	    return $arrayValues;
	}
	
	/**
	 * Function to get all award dates and award id 
	 *
	 * @return array
	 */
	public  function getAllAwardsDates()
	{ 
		$awardCollection = Mage::getModel('award/award')->getCollection()->addFieldToSelect(array('award_id','awarddate'))->getData(); 
		return $awardCollection;					
	}
}