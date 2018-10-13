<?php

class Krishinc_Autosendtrack_Block_Adminhtml_Autosendtrack_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('autosendtrack_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('autosendtrack')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('autosendtrack')->__('Item Information'),
          'title'     => Mage::helper('autosendtrack')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('autosendtrack/adminhtml_autosendtrack_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}