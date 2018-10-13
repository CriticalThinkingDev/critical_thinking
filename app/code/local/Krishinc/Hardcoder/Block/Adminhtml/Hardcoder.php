<?php
class Krishinc_Hardcoder_Block_Adminhtml_Hardcoder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_hardcoder';
    $this->_blockGroup = 'hardcoder';
    $this->_headerText = Mage::helper('hardcoder')->__('Manage Recommendation');
    $this->_addButtonLabel = Mage::helper('hardcoder')->__('Add New');
    parent::__construct();
  }
}