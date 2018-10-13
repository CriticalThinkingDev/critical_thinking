<?php
class Krishinc_Addressupdate_Helper_Data extends Mage_Core_Helper_Abstract
{	
	
	const XML_PATH_EMAIL_RECIPIENT  = 'addressupdate/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'addressupdate/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'addressupdate/email/email_template';
   
	 public function sendMail($post)
     {  
      
			$region = $post['region']; 
			if($regionId = $post['region_id']):
				$regionModel = Mage::getModel('directory/region')->load($regionId);
				$region = $regionModel->getName();
			endif;	
			$region2 = $post['region2']; 
			if($regionId2 = $post['region_id2']):
				$regionModel2 = Mage::getModel('directory/region')->load($regionId2);
				$region2 = $regionModel2->getName();
			endif;
		  	$country1 =  Mage::getModel('directory/country')->loadByCode($post['country_id']);
			$country2 =  Mage::getModel('directory/country')->loadByCode($post['country_id2']);
			$translate = Mage::getSingleton('core/translate');
			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);	 
			$post['country_id'] = $country1->getName();
			$post['country_id2']= $country2->getName();  
			$post['region'] = $region; 
			$post['region2'] = $region2;   
			$postObject = new Varien_Object();
			   $postObject->setData($post);
			   $mailTemplate = Mage::getModel('core/email_template');
			   //print_r(Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER));exit;
			   $sender['name'] = $post['firstname'].' '. $post['lastname'];
			   $sender['email'] = $post['email']; 
			/* @var $mailTemplate Mage_Core_Model_Email_Template */
			// $mailTemplate->addBcc('bijal@krishinc.com'); 
			$mailTemplate->setDesignConfig(array('area' => 'frontend'))
			    ->setReplyTo($post['email'])
			    ->sendTransactional(
			        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
			        $sender, 
			        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
			        null,
			        array('data' => $postObject)
			    );
			
			if (!$mailTemplate->getSentSuccess()) { 
			    throw new Exception();
			}

          $translate->setTranslateInline(true);
          
     }
	
	public function getPostUrl()
	{		 
        return $this->_getUrl('addressupdate/index/post', array('_secure'=>true));	
	}
}