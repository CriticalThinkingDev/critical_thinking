<?php

class Krishinc_Offerlanding_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	 

	public function items($filters)
	{
		$collection = Mage::getModel('offerlanding/offerlanding')->getCollection()
						->addFieldToSelect('*');
 		 Mage::log("Krishinc_Offerlanding_Model_Api: items called");
       
		  if (is_array($filters)) {
            try { 
                foreach ($filters as $field => $value) {
                	 
                   $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
            	 
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }
         if(sizeof($collection)> 0) {
	        $result = array();
			foreach ($collection as $collect)
			{
				$region = $collect->getRegion(); 
				if($regionId = $collect->getRegionId()):
					$regionModel = Mage::getModel('directory/region')->load($regionId);
					$region = $regionModel->getName();
					$collect->setRegion($region);
					
				endif;
				$collect->unsRegionId();
				$collect->unsStatus(); 
				$result[] = $collect->toArray();
			}   return $result;
         } else {
         	 $this->_fault('not_exists');
         	return false;  
         } 
	}
	
	public function create($offerlandingData)
	{ 
        try { 
        	/*$isEmailExist = Mage::getModel('offerlanding/offerlanding')->isEmailExists($offerlandingData['email']);
            if($isEmailExist)
            {
	            $this->_fault('Email Already Exists!'); 
	         	return false;  	
            }*/
        	$offerlanding = Mage::getModel('offerlanding/offerlanding')
                ->setData($offerlandingData)
                ->setCreatedTime(now())
                ->setUpdateTime(now())
                ->save(); 
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $offerlanding->getId(); 
	}
	
	public function info($offerlandingId)
	{ 
		$offerlanding = Mage::getModel('offerlanding/offerlanding')->load($offerlandingId);

        if (!$offerlanding->getId()) {
            $this->_fault('not_exists');
        }
         
   		$region = $offerlanding->getRegion(); 
		if($regionId = $offerlanding->getRegionId()):
			$regionModel = Mage::getModel('directory/region')->load($regionId);
			$region = $regionModel->getName();
			$offerlanding->setRegion($region); 
		endif;
		$offerlanding->unsRegionId();
		$offerlanding->unsStatus();    
	 
         
		return $offerlanding->toArray(); 
	}
	
	public function update($offerlandingId, $offerlandingData)
	{   
		$offerlanding = Mage::getModel('offerlanding/offerlanding')->load($offerlandingId);

        if (!$offerlanding->getId()) {
            $this->_fault('not_exists');
        }
        $offerlandingData['offerlanding_id'] = $offerlandingId;
       
        unset($offerlandingData['email']); 
        $offerlanding->setData($offerlandingData)
        				->setUpdateTime(now())->save();  
		  
        return "Successfully Updated entry. <br>Note: Email address will not be updated!";
	}
	 
	public function delete($offerlandingId)
	{
		 $offerlanding = Mage::getModel('offerlanding/offerlanding')->load($offerlandingId);

        if (!$offerlanding->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $offerlanding->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
	}
}