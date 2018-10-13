<?php

class Krishinc_Award_Block_Product_View_Award extends Mage_Core_Block_Template
{
	 
	protected $_awardCollection;
	
    function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
     
    public function getAwardsCollection()
    {
    	$product = $this->getProduct();
    	if($product) 
    	{
    		$productAwards = $product->getAward();
    	  	$pos = strpos($productAwards,',');
    		if ($pos === false)  
    		{  if(!empty($productAwards)) {
    			 $this->_awardCollection = Mage::getModel('award/award')->getCollection()  
				                			->addFieldToFilter('award_option_id', $productAwards);
    			}
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