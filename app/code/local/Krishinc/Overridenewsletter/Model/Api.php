<?php

class Krishinc_Overridenewsletter_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	 

	public function items($filters)
	{
		$collection = Mage::getModel('overridenewsletter/subscriber')->getCollection()
						->addFieldToSelect('*');
 		 Mage::log("Krishinc_Newsletter_Model_Api: items called");
       
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
	
	public function create($overridenewsletterData)
	{ 
        try {  
        	Mage::getModel('overridenewsletter/subscriber')->subscribe($overridenewsletterData);
        } catch (Mage_Core_Exception $e) { 
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $overridenewsletter->getId(); 
	}
	
	public function info($overridenewsletterId)
	{ 
		$overridenewsletter = Mage::getModel('overridenewsletter/subscriber')->load($overridenewsletterId);

        if (!$overridenewsletter->getId()) {
            $this->_fault('not_exists');
        }
         
		$result = array();
		$region = $overridenewsletter->getRegion(); 
		if($regionId = $overridenewsletter->getRegionId()):
			$regionModel = Mage::getModel('directory/region')->load($regionId);
			$region = $regionModel->getName();
			$overridenewsletter->setRegion($region); 
		endif;
		$overridenewsletter->unsRegionId();
		$overridenewsletter->unsStatus();   
         
		return $overridenewsletter->toArray(); 
	}
	
	/*public function update($overridenewsletterId, $overridenewsletterData)
	{   
		
		$overridenewsletter = Mage::getModel('overridenewsletter/subscriber')->load($overridenewsletterId);

        if (!$overridenewsletter->getId()) {
            $this->_fault('not_exists');
        }
        $overridenewsletterData['overridenewsletter_id'] = $overridenewsletterId;
       
        unset($overridenewsletterData['email']); 
        $overridenewsletter->setData($overridenewsletterData)
        				->setUpdateTime(now())->save(); 
		  return $overridenewsletter->getData();
        return "Successfully Updated entry. <br>Note: Email address will not be updated!";
	}
	 */
	public function delete($overridenewsletterId)
	{
		 $overridenewsletter = Mage::getModel('overridenewsletter/subscriber')->load($overridenewsletterId);

        if (!$overridenewsletter->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $overridenewsletter->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
	}
}