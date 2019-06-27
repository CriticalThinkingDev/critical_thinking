<?php
class Krishinc_Listrak_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_TO_GET_LISTRAK_ENABLE    = 'customer/listrak_config/enable';
	const XML_PATH_TO_GET_LISTRAK_USERNAME    = 'customer/listrak_config/listrak_username';
    const XML_PATH_TO_GET_LISTRAK_PASSWORD     = 'customer/listrak_config/listrak_password';
    const XML_PATH_TO_GET_LISTRAK_LISTID     = 'customer/listrak_config/listrak_listid';

	public function subscribeToListrack($data, $moduleName = '')
    {
    	if(Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_ENABLE)) {
    		
				$sh_param = array(
					'UserName' => Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_USERNAME), 
					'Password' => Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_PASSWORD) 
				); 
				
				$listID =   Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_LISTID);
				
				$contactProfileAttribute = $this->getMergeVars($data,$moduleName,false); 
				
				$authvalues = new SoapVar($sh_param, SOAP_ENC_OBJECT);
				    
				$headers[] = new SoapHeader("http://webservices.listrak.com/v31/", 'WSUser', $sh_param ); 
				
				$soapClient = new SoapClient("https://webservices.listrak.com/v31/IntegrationService.asmx?WSDL", array('trace'=> 1, 'exceptions' => true, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2));    
				 
				      
				$soapClient->__setSoapHeaders($headers);  
				 $mName = Mage::app()->getRequest()->getControllerModule();
				if($mName=='Krishinc_Offerlanding'){
$params = array(
				         'WSContact' => array(
				     		 'EmailAddress' => $data->getEmail(), 
				             'ListID' => $listID, 
				     		 'ContactProfileAttribute' => $contactProfileAttribute 
					  ),  
				      'ProfileUpdateType' => 'Overwrite',
				      'ExternalEventIDs' => "12911",
				      'OverrideUnsubscribe' => TRUE
				); 
                                 }elseif($mName=='Krishinc_Overridenewsletter'){
                    $params = array(
                        'WSContact' => array(
                            'EmailAddress' => $data->getEmail(),
                            'ListID' => $listID,
                            'ContactProfileAttribute' => $contactProfileAttribute
                        ),
                        'ProfileUpdateType' => 'Overwrite',
                        'ExternalEventIDs' => "5835",
                        'OverrideUnsubscribe' => TRUE
                    );

                }else{
$params = array(
				         'WSContact' => array(
				     		 'EmailAddress' => $data->getEmail(), 
				             'ListID' => $listID, 
				     		 'ContactProfileAttribute' => $contactProfileAttribute 
					  ),  
				      'ProfileUpdateType' => 'Overwrite',
				      'ExternalEventIDs' => "",
				      'OverrideUnsubscribe' => TRUE
				); 
}
				
Mage::log($params, null,'lskps.log');
				  
				try {   
				     $rest = $soapClient->SetContact($params);
				     $data->setListrakResponse($rest->SetContactResult);
				     $data->save();   
				     return true;
				
				} catch (SoapFault $e) {
				   	 $data->setListrakResponse($e->getMessage());
				     $data->save(); 
				     return  false; 
				}
    	}
    }

 public function subscribeToListrackebook($data, $moduleName = '')
    {

        if(Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_ENABLE)) {

            $sh_param = array(
                'UserName' => Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_USERNAME),
                'Password' => Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_PASSWORD)
            );
           
            $listID =   Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_LISTID);
            $contactProfileAttribute   = array();

            if($data->getSupply()){
	$contactProfileAttribute[] = array('AttributeID'=>1668452,'Value'=> $data->getSupply());
            }
            $contactProfileAttribute[] = array('AttributeID'=>1787030,'Value'=> 'on');
            $contactProfileAttribute[]  = array('AttributeID'=>2405405,'Value' => 'on');






            $authvalues = new SoapVar($sh_param, SOAP_ENC_OBJECT);

            $headers[] = new SoapHeader("http://webservices.listrak.com/v31/", 'WSUser', $sh_param );

            $soapClient = new SoapClient("https://webservices.listrak.com/v31/IntegrationService.asmx?WSDL", array('trace'=> 1, 'exceptions' => true, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2));


            $soapClient->__setSoapHeaders($headers);
             $externaleventid = "13236";

              if($data->getPagename()=="ebooksubscribe1"){
$externaleventid = "13517";
              }
if($data->getPagename()=="ebooksubscribe2"){
$externaleventid = "13915";
              }
            $params = array(
                'WSContact' => array(
                    'EmailAddress' => $data['email'],
                    'ListID' => $listID,
                    'ContactProfileAttribute' => $contactProfileAttribute
                ),
                'ProfileUpdateType' => 'Overwrite',
                'ExternalEventIDs' => $externaleventid,
                'OverrideUnsubscribe' => TRUE
            );
        
         
            try {
                $rest = $soapClient->SetContact($params);
                $data->setListrakResponse($rest->SetContactResult);
                $data->save();
                return true;

            } catch (SoapFault $e) {
                $data->setListrakResponse($e->getMessage());
                $data->save();
                return  false;
            }
        }
    }
 public function subscribeToListrackCustomer($data, $moduleName = '')
	{

		if(Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_ENABLE)) {

			$sh_param = array(
				'UserName' => Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_USERNAME),
				'Password' => Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_PASSWORD)
			);

			$listID =   Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_LISTID);
			$contactProfileAttribute   = array();

			$contactProfileAttribute[] = array('AttributeID'=>1668440,'Value'=>$data['firstname']);
			$contactProfileAttribute[] = array('AttributeID'=>1668441,'Value'=>$data['lastname']);
			$contactProfileAttribute[] = array('AttributeID'=>1787030,'Value'=> 'on');
			$contactProfileAttribute[]  = array('AttributeID'=>2405405,'Value' => 'on');






			$authvalues = new SoapVar($sh_param, SOAP_ENC_OBJECT);

			$headers[] = new SoapHeader("http://webservices.listrak.com/v31/", 'WSUser', $sh_param );

			$soapClient = new SoapClient("https://webservices.listrak.com/v31/IntegrationService.asmx?WSDL", array('trace'=> 1, 'exceptions' => true, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2));


			$soapClient->__setSoapHeaders($headers);

			$params = array(
				'WSContact' => array(
					'EmailAddress' => $data['email'],
					'ListID' => $listID,
					'ContactProfileAttribute' => $contactProfileAttribute
				),
				'ProfileUpdateType' => 'Overwrite',
				'ExternalEventIDs' => "",
				'OverrideUnsubscribe' => TRUE
			);


			try {
				$rest = $soapClient->SetContact($params);
				//$data->setListrakResponse($rest->SetContactResult);
				//$data->save();
				return true;

			} catch (SoapFault $e) {
				//$data->setListrakResponse($e->getMessage());
				//$data->save();
				return  false;
			}
		}
	}
    
    
    /**
	 * Return Merge Fields mapped to Magento attributes
	 *
	 * @param object $customer
	 * @param bool $includeEmail
	 * @param integer $websiteId
	 * @return array
	 */
	public function getMergeVars($dataObj, $modulename, $includeEmail = FALSE, $websiteId = NULL)
	{
		 
		$mergeVars   = array();
		$merge_vars   = array();
        $maps         = $this->getMergeMaps(Mage::app()->getStore()->getId(),$modulename); 

		if(!$maps){
			return;
		}
	 
		foreach($maps as $map){

			$customAtt = $map['magento'];
			$chimpTag  = $map['listrak_ids'];
			$mergeVars['AttributeID'] = '';
			$mergeVars['Value'] = '';
			if($chimpTag && $customAtt){
			    
			 	$mergeVars['AttributeID'] = $chimpTag; 
			 	 
				$getter = 'get'.trim($customAtt); 
				 
		 		 if($dataObj->$getter())
				 {
				 	 if($dataObj->$getter() != '') { 
					 	
					 		if((strtolower($customAtt) == 'market') &&($modulename == 'catalogrequest')) {  
									$mergeVars['Value'] = Mage::getModel($modulename.'/'.$modulename)->getMarketValue($dataObj->$getter());
						 } else { 
					 		
					 		$mergeVars['Value'] = $dataObj->$getter();
					 		 
					 	}
					 	
				 	 }
				 } 
				
				if(strtolower($customAtt) == 'region') 
			 	{ 
			 		
		 			$region = $dataObj->getRegion(); 
		 			$mergeVars['Value'] = $region;
		 			
					if($regionId = $dataObj->getRegionId()):
						$regionModel = Mage::getModel('directory/region')->load($regionId);
						$region = $regionModel->getName();
						$mergeVars['Value'] = $region;
					endif;  
			 		 
			 	}  
			 	if(!empty($mergeVars['Value'])){
 					$merge_vars[]= $mergeVars;
			 	}
			}
			
		}
		$merge_vars[]= array('AttributeID'=>'1787030','Value' => 'on');//this id is for Listrak CheckBox.Source.Web
		$customnewdata = Mage::app()->getRequest()->getParam('email_list');
		foreach($customnewdata as $chk){
			if($chk){
				$merge_vars[]= array('AttributeID'=>$chk,'Value' => 'on');
			}
		}
		$bsest_sales_news = Mage::app()->getRequest()->getParam('bsest_sales_news');

		if($bsest_sales_news=='2405405'){
			$merge_vars[]= array('AttributeID'=>$bsest_sales_news,'Value' => 'on');
		}

		$controllerName = Mage::app()->getRequest()->getControllerModule();

		if($controllerName=='Krishinc_Advancecustomer_Adminhtml' || $controllerName=='Mage_Customer' || $controllerName=='Mage_Newsletter' || $controllerName=='Krishinc_Overridepo_Adminhtml'){
			$merge_vars[]= array('AttributeID'=>2405405,'Value' => 'on');
		}
if($controllerName=='Krishinc_Overridepo_Adminhtml'){
			$position = Mage::app()->getRequest()->getParam('order');
			$positionVariable =$position['billing_address']['customer_type'];
			if($positionVariable){
				if($positionVariable=='P'){
					$value = 'Parent';
				}
				if($positionVariable=='H'){
					$value = 'Home Educator';
				}
				if($positionVariable=='E'){
					$value = 'Classroom Educator';
				}
				if($positionVariable=='D'){
					$value = 'Reseller';
				}
				if($positionVariable=='O'){
					$value = 'Parent';
				}
				$merge_vars[]= array('AttributeID'=>1668451,'Value' => $value);
			}

		}
		return $merge_vars;
	}
	
	
	/**
	 * Get module configuration value
	 *
	 * @param string $value
	 * @param string $store
	 * @return mixed Configuration setting
	 */
	public function config($value, $store = null)
	{
		$store = is_null($store) ? Mage::app()->getStore() : $store;

		$configscope = Mage::app()->getRequest()->getParam('store');
		if( $configscope && ($configscope !== 'undefined') ){
			$store = $configscope;
		}
 
		return Mage::getStoreConfig("customer/listrak_config/$value", $store);
	}
	
	
   /**
	 * Get config setting <map_fields>
	 *
	 * @return array|FALSE
	 */
	public function getMergeMaps($storeId,$prefix)
	{ 
		
		 return unserialize($this->config($prefix.'_map_fields', $storeId));
	 
	}
	
	/**
	 * Check if Krishinc_Listrak module is enabled
	 *
	 * @return bool
	 */
	public function canListrak()
	{
		return (bool)((int)$this->config('enable') !== 0);
	}
	/**
	 * Check if Krishinc_Listrak module >> catalogrequest is enabled
	 *
	 * @return bool
	 */
	public function canCatalogrequest()
	{
		return (bool)((int)$this->config('catalogrequest') !== 0);
	}
	
	/**
	 *Check if Krishinc_Listrak module >> Newsletter is enabled
	 *
	 * @return bool
	 */
	public function canNewsletter()
	{
		return (bool)((int)$this->config('overridenewsletter') !== 0);
	}
	
	/**
	 *Check if Krishinc_Listrak module >> offerlanding is enabled
	 *
	 * @return bool
	 */ 
	public function canOfferlanding()
	{ 
		return (bool)((int)$this->config('offerlanding') !== 0);
	}
	
	/**
	 *Check if Krishinc_Listrak module >> Custom Contactus is enabled
	 *
	 * @return bool
	 */ 
	public function canContactus()
	{ 
		return (bool)((int)$this->config('contactus') !== 0);
	}
	
	
	/**
	 *Check if Krishinc_Listrak module >> Fieldtester is enabled
	 *
	 * @return bool
	 */ 
	public function canFieldtester()
	{ 
		return (bool)((int)$this->config('fieldtester') !== 0);
	}
	/**
	 *Check if Krishinc_Listrak module >> Dealerskit is enabled
	 *
	 * @return bool
	 */ 
	public function canDealerskit()
	{ 
		return (bool)((int)$this->config('dealerskit') !== 0);
	}
public function subscribeToListrackWoobox($email){
        if(Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_ENABLE)) {
            $sh_param = array(
                'UserName' => Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_USERNAME),
                'Password' => Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_PASSWORD)
            );

            $listID =   Mage::getStoreConfig(self::XML_PATH_TO_GET_LISTRAK_LISTID);
            $contactProfileAttribute   = array();

            $contactProfileAttribute[] = array('AttributeID'=>1787030,'Value'=> 'on');
            $contactProfileAttribute[]  = array('AttributeID'=>2405405,'Value' => 'on');
            $contactProfileAttribute[]  = array('AttributeID'=>1668452,'Value' => 'quiz');


            $authvalues = new SoapVar($sh_param, SOAP_ENC_OBJECT);

            $headers[] = new SoapHeader("http://webservices.listrak.com/v31/", 'WSUser', $sh_param );
            $soapClient = new SoapClient("https://webservices.listrak.com/v31/IntegrationService.asmx?WSDL", array('trace'=> 1, 'exceptions' => true, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_version' => SOAP_1_2));
            $soapClient->__setSoapHeaders($headers);

            $params = array(
                'WSContact' => array(
                    'EmailAddress' => $email,
                    'ListID' => $listID,
                    'ContactProfileAttribute' => $contactProfileAttribute
                ),
                'ProfileUpdateType' => 'Overwrite',
                'ExternalEventIDs' => "13184",
                'OverrideUnsubscribe' => TRUE
            );
            
            try {
                $rest = $soapClient->SetContact($params);
                
                return true;

            } catch (SoapFault $e) {
               
                return  false;
            }

        }
    }
	
}
