<?php
class Krishinc_Award_Block_Adminhtml_Award extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_award';
        $this->_blockGroup = 'award';
        $this->_headerText = Mage::helper('award')->__('Award Manager');
        $this->_addButtonLabel = Mage::helper('award')->__('Add New Award');
		
        parent::__construct();
    }
}