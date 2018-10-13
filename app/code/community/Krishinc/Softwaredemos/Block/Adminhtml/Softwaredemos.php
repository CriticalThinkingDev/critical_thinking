<?php
class Krishinc_Softwaredemos_Block_Adminhtml_Softwaredemos extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  { 
    $this->_controller = 'adminhtml_softwaredemos';
    $this->_blockGroup = 'softwaredemos';
    $this->_headerText = Mage::helper('softwaredemos')->__('Software Demos  Manager');
    $this->_addButtonLabel = Mage::helper('softwaredemos')->__('Add Software Demos');
    parent::__construct();
  }
}