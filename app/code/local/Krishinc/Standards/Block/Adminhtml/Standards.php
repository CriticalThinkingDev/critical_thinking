<?php
class Krishinc_Standards_Block_Adminhtml_Standards extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_standards';
    $this->_blockGroup = 'standards';
    $this->_headerText = Mage::helper('standards')->__('Standard Manager');
    $this->_addButtonLabel = Mage::helper('standards')->__('Add Standard');
    parent::__construct();
  }
}