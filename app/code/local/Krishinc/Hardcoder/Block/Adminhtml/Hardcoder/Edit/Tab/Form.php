<?php

class Krishinc_Hardcoder_Block_Adminhtml_Hardcoder_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('hardcoder_form', array('legend'=>Mage::helper('hardcoder')->__('Item information')));

      $id = Mage::registry('hardcoder_data')->getData('hardcoder_id');
      $disabled = false;
      if($id){
          $disabled = true;
      }
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('hardcoder')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      $sattribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'subject');
      $subjectOption = $sattribute->getSource()->getAllOptions(true);

      $fieldset->addField('s_id', 'select', array(
          'label'     => Mage::helper('hardcoder')->__('Subject'),
          'name'      => 's_id',
          'disabled' => $disabled,
          'values'    => $subjectOption,
      ));

      $gattribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'grade');
      $gradeOption = $gattribute->getSource()->getAllOptions(true);
      $fieldset->addField('g_id', 'select', array(
          'label'     => Mage::helper('hardcoder')->__('Grade'),
          'name'      => 'g_id',
          'disabled' => $disabled,
          'values'    => $gradeOption,
      ));

 $pattribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'product_type');
      $typeOption = $pattribute->getSource()->getAllOptions(true);
      $fieldset->addField('p_id', 'multiselect', array(
          'label'     => Mage::helper('hardcoder')->__('Product Type'),
          'name'      => 'p_id',
          'disabled' => $disabled,
          'values'    => $typeOption,
      ));

      $fieldset->addField('sstatus', 'select', array(
          'label'     => Mage::helper('hardcoder')->__('Status'),
          'name'      => 'sstatus',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('hardcoder')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('hardcoder')->__('Disabled'),
              ),
          ),
      ));
     

     
      if ( Mage::getSingleton('adminhtml/session')->getHardcoderData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getHardcoderData());
          Mage::getSingleton('adminhtml/session')->setHardcoderData(null);
      } elseif ( Mage::registry('hardcoder_data') ) {
          $form->setValues(Mage::registry('hardcoder_data')->getData());
      }
      return parent::_prepareForm();
  }
}
