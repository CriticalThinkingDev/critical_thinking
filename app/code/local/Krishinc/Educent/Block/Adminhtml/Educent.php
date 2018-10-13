<?php
class Krishinc_Educent_Block_Adminhtml_Educent extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_educent';
    $this->_blockGroup = 'educent';
    $this->_headerText = Mage::helper('educent')->__('Track API Log');
    $this->_addButtonLabel = Mage::helper('educent')->__('Add Item');

    parent::__construct();
$this->_removeButton('add');
  }
}
