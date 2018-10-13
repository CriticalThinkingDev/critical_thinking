<?php

class Krishinc_Reviews_Helper_Data extends Mage_Review_Helper_Data
{
	public function getProduct()
	{
		return Mage::registry('product');
	}
    
   public function getReviewsUrl()
    { 
        return Mage::getUrl('review/product/list', array(
           'id'        => $this->getProduct()->getId(),
           'category'  => $this->getProduct()->getCategoryId()
        ));
    }
}