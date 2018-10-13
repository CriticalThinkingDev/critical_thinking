<?php
class Krishinc_Offerlanding_Block_Adminhtml_Offerlanding extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_offerlanding';
    $this->_blockGroup = 'offerlanding';
    $this->_headerText = Mage::helper('offerlanding')->__('Landing page Email Sign-up (Free gift) Manager');
    parent::__construct();
    $this->_removeButton('add');
  }
}