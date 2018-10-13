<?php

class Krishinc_Fieldtester_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	 

	public function items($filters)
	{
		$collection = Mage::getModel('fieldtester/fieldtester')->getCollection()
						->addFieldToSelect('*');
 		 Mage::log("Krishinc_Fieldtester_Model_Api: items called");
       
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
	
	public function create($fieldtesterData)
	{ 
        try { 
        	$isEmailExist = Mage::getModel('fieldtester/fieldtester')->isEmailExists($fieldtesterData['email']);
            if($isEmailExist)
            {
	            $this->_fault('Email Already Exists!'); 
	         	return false;  	
            }
        	$fieldtester = Mage::getModel('fieldtester/fieldtester')
                ->setData($fieldtesterData)
                ->setCreatedTime(now())
                ->setUpdateTime(now())
                ->save(); 
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $fieldtester->getId(); 
	}
	
	public function info($fieldtesterId)
	{ 
		$fieldtester = Mage::getModel('fieldtester/fieldtester')->load($fieldtesterId);

        if (!$fieldtester->getId()) {
            $this->_fault('not_exists');
        }
         
	  
		$region = $fieldtester->getRegion(); 
		if($regionId = $fieldtester->getRegionId()):
			$regionModel = Mage::getModel('directory/region')->load($regionId);
			$region = $regionModel->getName();
			$fieldtester->setRegion($region); 
		endif;
		$fieldtester->unsRegionId();
		$fieldtester->unsStatus();    
	 
         
		return $fieldtester->toArray(); 
	}
	
	public function update($fieldtesterId, $fieldtesterData)
	{   
		$fieldtester = Mage::getModel('fieldtester/fieldtester')->load($fieldtesterId);

        if (!$fieldtester->getId()) {
            $this->_fault('not_exists');
        }
        $fieldtesterData['fieldtester_id'] = $fieldtesterId;
       
        unset($fieldtesterData['email']); 
        $fieldtester->setData($fieldtesterData)
        				->setUpdateTime(now())->save(); 
		  
        return "Successfully Updated entry. <br>Note: Email address will not be updated!";
	}
	 
	public function delete($fieldtesterId)
	{
		 $fieldtester = Mage::getModel('fieldtester/fieldtester')->load($fieldtesterId);

        if (!$fieldtester->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $fieldtester->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
	}
}