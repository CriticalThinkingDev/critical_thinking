<?php

class Krishinc_Autosendtrack_Block_Adminhtml_Autosendtrack_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('autosendtrack_form', array('legend'=>Mage::helper('autosendtrack')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('autosendtrack')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('autosendtrack')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('autosendtrack')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('autosendtrack')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('autosendtrack')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('autosendtrack')->__('Content'),
          'title'     => Mage::helper('autosendtrack')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getAutosendtrackData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAutosendtrackData());
          Mage::getSingleton('adminhtml/session')->setAutosendtrackData(null);
      } elseif ( Mage::registry('autosendtrack_data') ) {
          $form->setValues(Mage::registry('autosendtrack_data')->getData());
      }
      return parent::_prepareForm();
  }
}