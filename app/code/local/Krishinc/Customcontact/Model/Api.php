<?php

class Krishinc_Customcontact_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	 

	public function items($filters)
	{
		$collection = Mage::getModel('customcontact/customcontact')->getCollection()
						->addFieldToSelect('*');
 		 Mage::log("Krishinc_Customcontact_Model_Api: items called");
       
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
	
	public function create($customcontactData)
	{ 
        try { 
        	 
        	$customcontact = Mage::getModel('customcontact/customcontact')
                ->setData($customcontactData)
                ->setCreatedTime(now())
                ->setUpdateTime(now())
                ->save(); 
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $customcontact->getId(); 
	}
	
	public function info($customcontactId)
	{ 
		$customcontact = Mage::getModel('customcontact/customcontact')->load($customcontactId);

        if (!$customcontact->getId()) {
            $this->_fault('not_exists');
        }
         
	  
		$region = $customcontact->getRegion(); 
		if($regionId = $customcontact->getRegionId()):
			$regionModel = Mage::getModel('directory/region')->load($regionId);
			$region = $regionModel->getName();
			$customcontact->setRegion($region); 
		endif;
		$customcontact->unsRegionId();
		$customcontact->unsStatus();    
	 
         
		return $customcontact->toArray(); 
	}
	
	public function update($customcontactId, $customcontactData)
	{   
		$customcontact = Mage::getModel('customcontact/customcontact')->load($customcontactId);

        if (!$customcontact->getId()) {
            $this->_fault('not_exists');
        }
        $customcontactData['customcontact_id'] = $customcontactId;
       
        unset($customcontactData['email']); 
        $customcontact->setData($customcontactData)
        				->setUpdateTime(now())->save(); 
		  
        return "Successfully Updated entry. <br>Note: Email address will not be updated!";
	}
	 
	public function delete($customcontactId)
	{
		 $customcontact = Mage::getModel('customcontact/customcontact')->load($customcontactId);

        if (!$customcontact->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $customcontact->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
	}
}