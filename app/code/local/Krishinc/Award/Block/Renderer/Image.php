<?php

class Krishinc_Award_Block_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['image'])) return '';
    	return '<img src="'. Mage::getBaseUrl('media')."award/". $row['image']. '" />';
    }

}