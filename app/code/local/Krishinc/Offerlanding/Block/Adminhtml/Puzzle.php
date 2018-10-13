<?php
class Krishinc_Offerlanding_Block_Adminhtml_Puzzle extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_puzzle';
        $this->_blockGroup = 'offerlanding';
        $this->_headerText = Mage::helper('offerlanding')->__('Puzzle of the Week Email Sign-up');
        parent::__construct();
        $this->_removeButton('add');
    }
}