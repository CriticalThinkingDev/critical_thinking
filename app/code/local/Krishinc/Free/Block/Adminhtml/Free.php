<?php
class Krishinc_Free_Block_Adminhtml_Free extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_free';
    $this->_blockGroup = 'free';
    $this->_headerText = Mage::helper('free')->__('Donation List');
    $this->_addButtonLabel = Mage::helper('free')->__('Add Item');
    parent::__construct();

    $this->removeButton('add');
  }
}
