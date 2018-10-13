<?php

class Krishinc_Hardcode_Block_Adminhtml_Hardcode_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('hardcode_form', array('legend'=>Mage::helper('hardcode')->__('Item information')));

      $id = Mage::registry('hardcode_data')->getData('hardcode_id');
      $disabled = false;
      if($id){
          $disabled = true;
      }
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('hardcode')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      $sattribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'subject');
      $subjectOption = $sattribute->getSource()->getAllOptions(true);

      $fieldset->addField('s_id', 'select', array(
          'label'     => Mage::helper('hardcode')->__('Subject'),
          'name'      => 's_id',
          'disabled' => $disabled,
          'values'    => $subjectOption,
      ));

      $gattribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'grade');
      $gradeOption = $gattribute->getSource()->getAllOptions(true);
      $fieldset->addField('g_id', 'select', array(
          'label'     => Mage::helper('hardcode')->__('Grade'),
          'name'      => 'g_id',
          'disabled' => $disabled,
          'values'    => $gradeOption,
      ));

      $fieldset->addField('sstatus', 'select', array(
          'label'     => Mage::helper('hardcode')->__('Status'),
          'name'      => 'sstatus',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('hardcode')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('hardcode')->__('Disabled'),
              ),
          ),
      ));
     

     
      if ( Mage::getSingleton('adminhtml/session')->getHardcodeData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getHardcodeData());
          Mage::getSingleton('adminhtml/session')->setHardcodeData(null);
      } elseif ( Mage::registry('hardcode_data') ) {
          $form->setValues(Mage::registry('hardcode_data')->getData());
      }
      return parent::_prepareForm();
  }
}