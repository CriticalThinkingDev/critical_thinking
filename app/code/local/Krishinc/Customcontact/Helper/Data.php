<?php

class Krishinc_Customcontact_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCustomcontactPostUrl()
	{		 
        return $this->_getUrl('customcontact/index/createpost', array('_secure'=>true));		
	}

	public function getAllSubjects()
	{
		return array(    
                           'YourOrder' => 'Your Order' ,
                           'OurWebsite' => 'Our Website' ,
                           'BookProducts' => 'Book Products' ,
                           'SoftwareProducts' => 'Software Products' ,
                           'NewProductIdeas' => 'New Product Ideas' ,
                           'Permissions' => 'Permissions' ,
                           'ResellerQuestions' => 'Reseller Questions',
                           'SuccessStory' => 'Success Story' ,
                           'Remove' => 'Remove from Mailing List' ,
                           'Unsubscribe' => 'Unsubscribe from Newsletter' ,
                           'Quote' => 'Request a Quote' ,
                           'Other' => 'Other' , 
		);
	}		
	
	public function getAllFoundvia()
	{
		return array(     
                           'SearchEngine' => 'Search Engine' ,
                           'Advertisement' => 'Advertisement' ,
                           'Friend' => 'Friend' ,
                           'Catalog' => 'Our Catalog' ,
                           'Product' => 'Product' ,
                           'SocialMedia' => 'Facebook, Twitter, etc.' ,
                           'Other' => 'Other' ,
                         
		);
	}	
	 
}