<?php

class Krishinc_Catalogrequest_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	 

	public function items($filters)
	{
		$collection = Mage::getModel('catalogrequest/catalogrequest')->getCollection()
						->addFieldToSelect('*');
 		 Mage::log("Krishinc_Catalogrequest_Model_Api: items called");
       
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
	
	public function create($catalogrequestData)
	{ 
        try { 
        	 
        	$catalogrequest = Mage::getModel('catalogrequest/catalogrequest')
                ->setData($catalogrequestData)
                ->setCreatedTime(now())
                ->setUpdateTime(now())
                ->save(); 
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $catalogrequest->getId(); 
	}
	
	public function info($catalogrequestId)
	{ 
		$catalogrequest = Mage::getModel('catalogrequest/catalogrequest')->load($catalogrequestId);

        if (!$catalogrequest->getId()) {
            $this->_fault('not_exists');
        }
         
		$result = array();
		$region = $catalogrequest->getRegion(); 
		if($regionId = $catalogrequest->getRegionId()):
			$regionModel = Mage::getModel('directory/region')->load($regionId);
			$region = $regionModel->getName();
			$catalogrequest->setRegion($region); 
		endif;
		$catalogrequest->unsRegionId();
		$catalogrequest->unsStatus();   
         
		return $catalogrequest->toArray(); 
	}
	
	public function update($catalogrequestId, $catalogrequestData)
	{   
		
		$catalogrequest = Mage::getModel('catalogrequest/catalogrequest')->load($catalogrequestId);

        if (!$catalogrequest->getId()) {
            $this->_fault('not_exists');
        }
        $catalogrequestData['catalogrequest_id'] = $catalogrequestId;
       
        unset($catalogrequestData['email']); 
        $catalogrequest->setData($catalogrequestData)
        				->setUpdateTime(now())->save(); 
		  return $catalogrequest->getData();
        return "Successfully Updated entry. <br>Note: Email address will not be updated!";
	}
	 
	public function delete($catalogrequestId)
	{
		 $catalogrequest = Mage::getModel('catalogrequest/catalogrequest')->load($catalogrequestId);

        if (!$catalogrequest->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $catalogrequest->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
	}
}