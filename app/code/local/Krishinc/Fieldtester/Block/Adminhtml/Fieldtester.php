<?php
class Krishinc_Fieldtester_Block_Adminhtml_Fieldtester extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_fieldtester';
    $this->_blockGroup = 'fieldtester';
    $this->_headerText = Mage::helper('fieldtester')->__('Fieldtester Manager');
    parent::__construct();
    $this->_removeButton('add');
  }
}