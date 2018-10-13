<?php

class Hm_Testimonial_Block_Adminhtml_Testimonial_Media extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        if (empty($row['media'])) return '';
        return '<img src="'. Mage::getBaseUrl('media'). $row['media']. '" width="100" height="100" />';
    }

}