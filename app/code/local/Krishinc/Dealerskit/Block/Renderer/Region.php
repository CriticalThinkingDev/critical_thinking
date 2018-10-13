<?php

class Krishinc_Dealerskit_Block_Renderer_Region extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (!empty($row['region'])) {
    		$region = $row['region'];
    	} else {
    		 
				if($regionId = $row['region_id']):
					$regionModel = Mage::getModel('directory/region')->load($regionId);
					$region = $regionModel->getName();
				endif;
    	}
    	 return $region;
    }

}