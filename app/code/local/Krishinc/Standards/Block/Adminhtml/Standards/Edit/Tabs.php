<?php

class Krishinc_Standards_Block_Adminhtml_Standards_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('standards_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('standards')->__('Standard Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('standards')->__('Standard Data'),
          'title'     => Mage::helper('standards')->__('Standard Data'),
          'content'   => $this->getLayout()->createBlock('standards/adminhtml_standards_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}