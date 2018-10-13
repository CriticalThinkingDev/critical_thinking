<?php
class Krishinc_Dealerskit_Block_Adminhtml_Dealerskit extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_dealerskit';
    $this->_blockGroup = 'dealerskit';
    $this->_headerText = Mage::helper('dealerskit')->__('Dealer Kits Request Manager');
    parent::__construct();
    $this->_removeButton('add');
  }
}