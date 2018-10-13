<?php
class Krishinc_Reviews_Block_Helper extends Mage_Review_Block_Helper 
{
	 public function getReviewsUrl()
    { 
        return Mage::getUrl('review/product/list', array(
           'id'        => Mage::registry('product')->getId(),
           'category'  => Mage::registry('product')->getCategoryId()
        ));
    }

}