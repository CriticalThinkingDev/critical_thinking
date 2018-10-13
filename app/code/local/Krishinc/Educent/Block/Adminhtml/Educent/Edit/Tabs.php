<?php

class Krishinc_Educent_Block_Adminhtml_Educent_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('educent_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('educent')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('educent')->__('Item Information'),
          'title'     => Mage::helper('educent')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('educent/adminhtml_educent_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}