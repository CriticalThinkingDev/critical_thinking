<?php
class Krishinc_Customcontact_Block_Adminhtml_Customcontact extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_customcontact';
    $this->_blockGroup = 'customcontact';
    $this->_headerText = Mage::helper('customcontact')->__('Contact Us Manager');
    parent::__construct();
    $this->_removeButton('add');
  }
}