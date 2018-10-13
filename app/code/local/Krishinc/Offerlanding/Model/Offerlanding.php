<?php

class Krishinc_Offerlanding_Model_Offerlanding extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('offerlanding/offerlanding');
    }
    
    /**
     * * Function to send listrack data
     *
     * @param unknown_type $data
     */
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
//						array('AttributeID' => 1668444, 'Value' => $data->getAddress1()),
//						array('AttributeID' => 1668445, 'Value' => $data->getAddress2()),
//						array('AttributeID' => 1668446, 'Value' => $data->getCity()),
//						array('AttributeID' => 1668447, 'Value' => $region),
//						array('AttributeID' => 1668448, 'Value' => $data->getZipcode()),
//						array('AttributeID' => 1668449, 'Value' => $data->getCountry()), 
//						array('AttributeID' => 1668450, 'Value' => $data->getPhone()),
//						array('AttributeID' => 1668451, 'Value' =>  $data->getBestDescribe() ),
//						array('AttributeID' => 1668452, 'Value' => $data->getSupply())
//				   )
//			  ),  
//		      'ProfileUpdateType' => 'Overwrite',
//		      'ExternalEventIDs' => "4832",
//		      'OverrideUnsubscribe' => TRUE
//		); 
//		  
//		try { 
//			 
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
//     public function isEmailExists($email)
//    {
//    	$data = $this->getCollection()
//    		->addFieldToFilter('email',array('eq',$email))->getFirstItem();
//    	if($data)
//    	{
//    		return $data->getId();
//    	} else {
//    		return false;
//    	}
//    }
    
}