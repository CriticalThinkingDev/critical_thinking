<?php
 
class Krishinc_Reviews_Block_Product_View extends Mage_Review_Block_Product_View
{
    public function getReviewsCollection()
    { 
       $this->_reviewsCollection = parent::getReviewsCollection();
       $this->_reviewsCollection->setOrder('detail_id','desc');  
       return $this->_reviewsCollection;
        
    }

}
