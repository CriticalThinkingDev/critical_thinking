<?php

class Krishinc_Dealerskit_Model_Dealerskit extends Mage_Core_Model_Abstract
{
	const XML_PATH_EMAIL_RECIPIENT  = 'dealerskit/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'dealerskit/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'dealerskit/email/email_template';
    const XML_PATH_ENABLED          = 'dealerskit/dealerskit/enabled';
    public function _construct()
    {
        parent::_construct();
        $this->_init('dealerskit/dealerskit');
    }
      
 
    
//    public function getMarketValue($bestdesc)
//    {
//    	
//		$allMarketValues =	array('Preschool Teacher or Coordinator'  => 'Classroom Educator',
//			'K-2 Teacher' => 'Classroom Educator',
//			'3-6 Teacher' => 'Classroom Educator',
//			'K-6 Coordinator or Administrator' => 'Classroom Educator',
//			'Jr. High Teacher or Coordinator or Admin' => 'Classroom Educator',
//			'High School Teacher or Coordinator or Admin' => 'Classroom Educator',
//			'K-12 Coordinator or Administrator' => 'Classroom Educator',
//			'College Instructor' => 'Classroom Educator',
//			'School of Ed. Professor' => 'Classroom Educator',
//			'Adult Ed. Teacher' => 'Classroom Educator',
//			'Educational Consultant' => 'Classroom Educator',
//			'Therapist' => 'Classroom Educator',
//			'Tutor' => 'Classroom Educator',
//			'Parent' => 'Parent',
//			'Home Educator' => 'Home Educator',
//			'Other' => 'Parent'); 
//			return $allMarketValues[$bestdesc];
//
//    }
    
   
     public function sendMail($post)
     { 
			$region = $post['region']; 
			if($regionId = $post['region_id']):
				$regionModel = Mage::getModel('directory/region')->load($regionId);
				$region = $regionModel->getName();
			endif;
		   $translate = Mage::getSingleton('core/translate');
           /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);	
		   $post['region'] = $region;
		   $postObject = new Varien_Object();
      	   $postObject->setData($post);
     	   $mailTemplate = Mage::getModel('core/email_template');
            /* @var $mailTemplate Mage_Core_Model_Email_Template */
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->setReplyTo($post['email'])
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                    null,
                    array('data' => $postObject)
                );

            if (!$mailTemplate->getSentSuccess()) { 
                throw new Exception();
            }

          $translate->setTranslateInline(true);
          
     }
}