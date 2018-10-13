<?php

class Krishinc_Educent_Block_Adminhtml_Educent_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('educent_form', array('legend'=>Mage::helper('educent')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('educent')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('educent')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('educent')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('educent')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('educent')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('educent')->__('Content'),
          'title'     => Mage::helper('educent')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getEducentData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEducentData());
          Mage::getSingleton('adminhtml/session')->setEducentData(null);
      } elseif ( Mage::registry('educent_data') ) {
          $form->setValues(Mage::registry('educent_data')->getData());
      }
      return parent::_prepareForm();
  }
}