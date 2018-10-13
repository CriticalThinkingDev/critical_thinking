<?php
class Krishinc_Catalogrequest_Block_Adminhtml_Catalogrequest extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_catalogrequest';
    $this->_blockGroup = 'catalogrequest';
    $this->_headerText = Mage::helper('catalogrequest')->__('Catalog Request Manager');
    parent::__construct();
    $this->_removeButton('add');
  }
}