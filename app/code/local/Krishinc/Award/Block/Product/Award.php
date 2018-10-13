<?php

class Krishinc_Award_Block_Product_Award extends Mage_Catalog_Block_Product_View
{
	 
	protected $_awardCollection;
	 
    public function getAwardsCollection($product)
    {
    	if($product)
    	{
    		$productAwards = $product->getAward();
    	  	$pos = strpos($productAwards,',');
    		if ($pos === false)  
    		{
    			 
    			 $this->_awardCollection[] = Mage::getModel('award/award')->getCollection()  
				                			->addFieldToFilter('award_option_id', $productAwards);
	   		} else {
    			
    			$arrProductAwards = explode(',',$productAwards);  
				foreach ($arrProductAwards as $awId)
				{
					  $this->_awardCollection[] = Mage::getModel('award/award')->getCollection()  
		                ->addFieldToFilter('award_option_id', $awId); 
				}           
    		}
 
 
    	}
    
        return $this->_awardCollection;
    }
}