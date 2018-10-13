<?php

class Krishinc_Hardcode_Block_Adminhtml_Hardcode_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('hardcode_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('hardcode')->__('Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('hardcode')->__('Grade and Subject'),
          'title'     => Mage::helper('hardcode')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('hardcode/adminhtml_hardcode_edit_tab_form')->toHtml(),
      ));

      $this->addTab('form_section1', array(
          'label'     => Mage::helper('hardcode')->__('Manage Products'),
          'url'       => $this->getUrl('*/*/associatedproducts', array('_current' => true)),
          'class'     => 'ajax',
      ));


      return parent::_beforeToHtml();
  }
}