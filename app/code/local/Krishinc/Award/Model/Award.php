<?php
class Krishinc_Award_Model_Award extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('award/award');
    }
    
    public function updateOptionsToAwards($options)
    {
//		$model = Mage::getModel('catalog/resource_eav_attribute')->load('award', 'attribute_code');
//		$model->addData($options); 
//		$model->save();
//		$model1 = Mage::getModel('catalog/resource_eav_attribute')->load('award', 'attribute_code');
//		$allOptions = $model1->getSource()->getAllOptions(false);
//            echo '<pre>';
//			print_r($allOptions);exit;
    	if($options)
    	{  
    		foreach ($options['value'] as $optionId => $value)
    		{
    			$model = Mage::getModel('award/award');
    			$data = $model->load($optionId); 
    			if($data->getId())
    			{  
	    			$data->setName($value[0])
	    				 ->save();
	    				 
    			} else {
    				
    				$model->setId($optionId)
	    				 ->setAwardOptionId($optionId)
	    				 ->setName($value[0]) 
	    				 ->save();
    			}    			
    		}
    		  
    		foreach($options['delete'] as $optionId => $value)
    		{ 
    			if(!empty($value))
    			{
    				$model->load($optionId);  
                    $image = $model->getImage();
                    $filepath = Mage::getBaseDir('media')."\award\\".$image;  
                    unlink($filepath);                    
                    $model->delete();
    			}
			}
    		
		}
	} 
	  
     
}