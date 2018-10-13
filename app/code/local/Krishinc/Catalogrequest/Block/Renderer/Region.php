<?php

class Krishinc_Catalogrequest_Block_Renderer_Region extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
//    	if (!empty($row['region'])) {
//    		$region = '';//$row['region'];
//    	} else {
//We have to show only Region code in CSV file
    	$region = ''; 
		if($regionId = $row['region_id']):
			$regionModel = Mage::getModel('directory/region')->load($regionId);
			$region = $regionModel->getCode();
		endif;
//    	}
    	 return $region;
    }

}