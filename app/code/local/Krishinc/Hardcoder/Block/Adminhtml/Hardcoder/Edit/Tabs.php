<?php

class Krishinc_Hardcoder_Block_Adminhtml_Hardcoder_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('hardcoder_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('hardcoder')->__('Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('hardcoder')->__('Grade and Subject'),
          'title'     => Mage::helper('hardcoder')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('hardcoder/adminhtml_hardcoder_edit_tab_form')->toHtml(),
      ));

      $this->addTab('form_section1', array(
          'label'     => Mage::helper('hardcoder')->__('Manage Products'),
          'url'       => $this->getUrl('*/*/associatedproducts', array('_current' => true)),
          'class'     => 'ajax',
      ));


      return parent::_beforeToHtml();
  }
}