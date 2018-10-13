<?php
class Krishinc_Teachingsupport_Block_Adminhtml_Teachingsupport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_teachingsupport';
    $this->_blockGroup = 'teachingsupport';
    $this->_headerText = Mage::helper('teachingsupport')->__('Teaching Support Manager');
    $this->_addButtonLabel = Mage::helper('teachingsupport')->__('Add Item');
    parent::__construct();
  }
}