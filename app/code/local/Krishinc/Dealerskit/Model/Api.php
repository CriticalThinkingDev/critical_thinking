<?php

class Krishinc_Dealerskit_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	 

	public function items($filters)
	{
		$collection = Mage::getModel('dealerskit/dealerskit')->getCollection()
						->addFieldToSelect('*');
 		 Mage::log("Krishinc_Dealerskit_Model_Api: items called");
       
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
			{	$region = $collect->getRegion(); 
				if($regionId = $collect->getRegionId()):
					$regionModel = Mage::getModel('directory/region')->load($regionId);
					$region = $regionModel->getName();
					$collect->setRegion($region);
					
				endif;
				$collect->unsRegionId();
				$collect->unsStatus(); 
				$result[] = $collect->toArray();
			}  
			return $result;
         } else {
         	 $this->_fault('not_exists');
         	return false;  
         } 
	}
	
	public function create($dealerskitData)
	{ 
        try { 
        	$isEmailExist = Mage::getModel('dealerskit/dealerskit')->isEmailExists($dealerskitData['email']);
            if($isEmailExist)
            {
	            $this->_fault('Email Already Exists!'); 
	         	return false;  	
            }
        	$dealerskit = Mage::getModel('dealerskit/dealerskit')
                ->setData($dealerskitData)
                ->setCreatedTime(now())
                ->setUpdateTime(now())
                ->save(); 
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $dealerskit->getId(); 
	}
	
	public function info($dealerskitId)
	{ 
		$dealerskit = Mage::getModel('dealerskit/dealerskit')->load($dealerskitId);

        if (!$dealerskit->getId()) {
            $this->_fault('not_exists');
        }
         
		$result = array();
		$region = $dealerskit->getRegion(); 
		if($regionId = $dealerskit->getRegionId()):
			$regionModel = Mage::getModel('directory/region')->load($regionId);
			$region = $regionModel->getName();
			$dealerskit->setRegion($region); 
		endif;
		$dealerskit->unsRegionId();
		$dealerskit->unsStatus();   
         
		return $dealerskit->toArray(); 
	}
	
	public function update($dealerskitId, $dealerskitData)
	{   
		
		$dealerskit = Mage::getModel('dealerskit/dealerskit')->load($dealerskitId);

        if (!$dealerskit->getId()) {
            $this->_fault('not_exists');
        }
        $dealerskitData['dealerskit_id'] = $dealerskitId;
       
        unset($dealerskitData['email']); 
        $dealerskit->setData($dealerskitData)
        				->setUpdateTime(now())->save(); 
		  return $dealerskit->getData();
        return "Successfully Updated entry. <br>Note: Email address will not be updated!";
	}
	 
	public function delete($dealerskitId)
	{
		 $dealerskit = Mage::getModel('dealerskit/dealerskit')->load($dealerskitId);

        if (!$dealerskit->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $dealerskit->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
	}
}