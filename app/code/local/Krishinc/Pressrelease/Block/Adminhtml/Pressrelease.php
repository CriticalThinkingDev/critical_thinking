<?php
class Krishinc_Pressrelease_Block_Adminhtml_Pressrelease extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_pressrelease';
    $this->_blockGroup = 'pressrelease';
    $this->_headerText = Mage::helper('pressrelease')->__('Press Release Manager');
    $this->_addButtonLabel = Mage::helper('pressrelease')->__('Add Press Release');
    parent::__construct();
  }
}