<?php

class Krishinc_Teachingsupport_Block_Adminhtml_Teachingsupport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('teachingsupport_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('teachingsupport')->__('Teaching Support Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('teachingsupport')->__('Teaching Support Information'),
          'title'     => Mage::helper('teachingsupport')->__('Teaching Support Information'),
          'content'   => $this->getLayout()->createBlock('teachingsupport/adminhtml_teachingsupport_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}