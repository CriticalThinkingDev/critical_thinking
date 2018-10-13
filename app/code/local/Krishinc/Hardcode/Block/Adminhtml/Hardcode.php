<?php
class Krishinc_Hardcode_Block_Adminhtml_Hardcode extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_hardcode';
    $this->_blockGroup = 'hardcode';
    $this->_headerText = Mage::helper('hardcode')->__('Manage Hard Code Search');
    $this->_addButtonLabel = Mage::helper('hardcode')->__('Add New');
    parent::__construct();
  }
}