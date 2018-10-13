<?php
class Krishinc_Reviews_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Review_Grid
 {
	 
    protected function _prepareColumns()
    { 
	    $this->addColumnAfter('location', array(
            'header'        => Mage::helper('review')->__('Location'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.location',
            'index'         => 'location',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true, 
        ),'nickname');
  	   parent::_prepareColumns();
    }
}
?>