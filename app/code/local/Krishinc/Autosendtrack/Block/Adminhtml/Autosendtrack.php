<?php
class Krishinc_Autosendtrack_Block_Adminhtml_Autosendtrack extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_autosendtrack';
    $this->_blockGroup = 'autosendtrack';
    $this->_headerText = Mage::helper('autosendtrack')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('autosendtrack')->__('Add Item');
    parent::__construct();
  }
}