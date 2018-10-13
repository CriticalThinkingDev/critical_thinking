<?php

class Krishinc_Pressrelease_Block_Adminhtml_Pressrelease_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('pressrelease_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('pressrelease')->__('Press Release Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('pressrelease')->__('Press Release Information'),
          'title'     => Mage::helper('pressrelease')->__('Press Release Information'),
          'content'   => $this->getLayout()->createBlock('pressrelease/adminhtml_pressrelease_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}