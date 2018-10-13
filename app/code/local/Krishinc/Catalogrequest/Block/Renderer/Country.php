<?php

class Krishinc_Catalogrequest_Block_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	$countryCode='';
    	if (!empty($row['country_id'])) {
    		$countryId = $row['country_id'];
			$countryModel = Mage::getModel('directory/country')->load($countryId);
//			print_r($countryModel->getData());exit;
			$countryCode = $countryModel->getIso3Code();
    	}  
    	 return $countryCode;
    }

}