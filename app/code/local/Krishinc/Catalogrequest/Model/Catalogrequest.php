<?php

class Krishinc_Catalogrequest_Model_Catalogrequest extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('catalogrequest/catalogrequest');
    }
    public function isEmailExists($email)
    {
    	$data = $this->getCollection()
    		->addFieldToFilter('email',array('eq',$email))->getFirstItem();
    	if($data)
    	{
    		return $data->getId();
    	} else {
    		return false;
    	}
    }

//    public function subscribeToListrack($data)
//    {
//		$sh_param = array(
//			'UserName' => "ktpl", 
//			'Password' => "Magento123"
//		); 
//
//		
//		$authvalues = new SoapVar($sh_param, SOAP_ENC_OBJECT);
//		    
//		$headers[] = new SoapHeader("http://webservices.listrak.com/v31/", 'WSUser', $sh_param ); 
//		
//		$soapClient = new SoapClient("https://webservices.listrak.com/v31/IntegrationService.asmx?WSDL", array('trace'=> 1, 'exceptions' => true, 
//		    'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2));
//		     
//		      
//		$soapClient->__setSoapHeaders($headers); 
//		$region = $data->getRegion(); 
//		if($regionId = $data->getRegionId()):
//			$regionModel = Mage::getModel('directory/region')->load($regionId);
//			$region = $regionModel->getName();
//		endif;
//		
//		$params = array(
//		         'WSContact' => array(
//		     		 'EmailAddress' => $data->getEmail(), 
//		             'ListID' => 285473, 
//		     		 'ContactProfileAttribute' => array(
//						array('AttributeID' => 1668440, 'Value' => $data->getFirstname()),
//						array('AttributeID' => 1668441, 'Value' => $data->getLastname()),
//						array('AttributeID' => 1668442, 'Value' => $data->getSchoolname()),
//						array('AttributeID' => 1668443, 'Value' => ''),
//						array('AttributeID' => 1668444, 'Value' => $data->getMailingAddress()),
//						array('AttributeID' => 1668445, 'Value' => $data->getApptUnit()),
//						array('AttributeID' => 1668446, 'Value' => $data->getCity()),
//						array('AttributeID' => 1668447, 'Value' => $region),
//						array('AttributeID' => 1668448, 'Value' => $data->getZipcode()),
//						array('AttributeID' => 1668449, 'Value' => $data->getCountry()), 
//						array('AttributeID' => 1668450, 'Value' => $data->getPhone()),
//						array('AttributeID' => 1668451, 'Value' => $this->getMarketValue($data->getMarket())) 
//						 
//				   )
//			  ),  
//		      'ProfileUpdateType' => 'Overwrite',
//		      'ExternalEventIDs' => "4832",
//		      'OverrideUnsubscribe' => TRUE
//		); 
//		  
//		try { 
//		     $rest = $soapClient->SetContact($params);
//		     $data->setListrakResponse($rest->SetContactResult);
//		     $data->save();  
//		
//		} catch (SoapFault $e) {
//		   	 $data->setListrakResponse($e->getMessage());
//		     $data->save(); 
//		}
//    }
//    
    
    
    public function getMarketValue($bestdesc)
    {
    	
		$allMarketValues =	array('Preschool Teacher or Coordinator'  => 'Classroom Educator',
			'K-2 Teacher' => 'Classroom Educator',
			'3-6 Teacher' => 'Classroom Educator',
			'K-6 Coordinator or Administrator' => 'Classroom Educator',
			'Jr. High Teacher or Coordinator or Admin' => 'Classroom Educator',
			'High School Teacher or Coordinator or Admin' => 'Classroom Educator',
			'K-12 Coordinator or Administrator' => 'Classroom Educator',
			'College Instructor' => 'Classroom Educator',
			'School of Ed. Professor' => 'Classroom Educator',
			'Adult Ed. Teacher' => 'Classroom Educator',
			'Educational Consultant' => 'Classroom Educator',
			'Therapist' => 'Classroom Educator',
			'Tutor' => 'Classroom Educator',
			'Parent' => 'Parent',
			'Home Educator' => 'Home Educator',
			'Other' => 'Parent'); 
			return $allMarketValues[$bestdesc];

    }
    
   
    
//    public function insertIntoMailChimp($data)
//    {
//    	$listId = 'ed0a23061c';
//    	 
//	   	$store = Mage::app()->getStore();
//    	$IsActive      = Mage::getStoreConfig("monkey/general/active", $store);
//    	if((bool)((int)$IsActive !== 0)): 
//	    	$api       = Mage::getSingleton('monkey/api');
//	 		$email = $data->getEmail();
//			$region = $data->getRegion(); 
//			if($regionId = $data->getRegionId()):
//				$regionModel = Mage::getModel('directory/region')->load($regionId);
//				$region = $regionModel->getName();
//			endif;
//			$groups = array(); 
//
//			$groupId = (int)Mage::getStoreConfig("monkey/groupings/list_" . $listId, $store->getId());
//			if($groupId){
//				$groups = Mage::helper('customer')->getGroups()->toOptionHash();
//				$groupings[] = array(
//					'id'     => $groupId,
//					'groups' => $groups[1],
//				);
//			}
//			 
//		 
//	 		 $mergeVars = array(
//	 							'FNAME'=>	$data->getFirstname(),
//	 							'LNAME'=>	$data->getLastname(), 
//	 							'MADDRESS'=>	$data->getAddress(), 
//	 							'CITY'	  =>	$data->getCity(),
//	 							'STATE'	  =>	$region,
//								'COUNTRY' =>	$data->getCountry(),
//	 							'ZIPCODE' =>	$data->getZipcode(),
//	 							'PHONE'	  =>	$data->getPhone(),
//	 							'STOREID' =>    $store->getId(), 
//	 					 );  
//	 					 
//	 					 
//			 $mergeVars['GROUPINGS'] = $groupings;	 
//			 
//			//Handle groups update
//		$abc = 	 Mage::getSingleton('monkey/api')
//								->listSubscribe($listId, $email,  $mergeVars,'html',true ); 
//		  
//		endif;
//    } 
}