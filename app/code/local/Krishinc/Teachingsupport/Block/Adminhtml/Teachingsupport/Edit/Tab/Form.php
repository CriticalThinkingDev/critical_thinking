<?php

class Krishinc_Teachingsupport_Block_Adminhtml_Teachingsupport_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('teachingsupport_form', array('legend'=>Mage::helper('teachingsupport')->__('Item information')));
      $model = Mage::getModel('teachingsupport/teachingsupport');
      
      $pdf1 = '';
      $pdf2 = '';
      if($id = Mage::app()->getRequest()->getParam('id'))
      {
      	$data =$model->load($id);
      	$pdf1 = Mage::getBaseUrl('media').$data->getPdf();
      	if($pdf2 = $data->getPdf1())
      	{
      		$pdf2 = Mage::getBaseUrl('media').$data->getPdf1();	 
      	}
      	
      }
      
      
      $fieldset->addField('sku', 'multiselect', array(
          'label'     => Mage::helper('teachingsupport')->__('Products'),
          'class'     => 'required-entry',
          'required'  => true,
          'style'     => 'width:500px;height:300px;',
          'name'      => 'sku[]',
          'values'	  => $model->getProductSkuOptionArray()
      ));
	  $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('teachingsupport')->__('Title'),
          'required'  => true,
          'name'      => 'title',
	  ));   

	  $fieldset->addField('type', 'text', array(
          'label'     => Mage::helper('teachingsupport')->__('Type'),
          'required'  => true,
          'name'      => 'type',
	  ));   
      $fieldset->addField('pdf_title1', 'text', array(
          'label'     => Mage::helper('teachingsupport')->__('PDF Title#1'),
          'required'  => true,
          'name'      => 'pdf_title1',
	  ));   
	   $fieldset->addField('pdf', 'file', array(
          'label'     => Mage::helper('teachingsupport')->__('PDF File#1'),
          'required'  => ($pdf1?false:true),
          'name'      => 'pdf', 
          'after_element_html' => ($pdf1?'<br/><a href="'.$pdf1.'" target="_blank">'.$data->getPdf().'</a>':'<br/>Please upload only pdf file.'),
	  ));
     $fieldset->addField('pdf_title2', 'text', array(
          'label'     => Mage::helper('teachingsupport')->__('PDF Title#2'),
          'required'  => false,
          'name'      => 'pdf_title2',
          
	  ));   
	 $fieldset->addField('pdf1', 'file', array(
          'label'     => Mage::helper('teachingsupport')->__('PDF File#2'),
          'required'  => false,
          'name'      => 'pdf1',
          'after_element_html' => ($pdf2?'<br/><a href="'.$pdf2.'" target="_blank">'.$data->getPdf1().'</a>':'<br/>Please upload only pdf file.'),
	  ));
		 
     
     
      if ( Mage::getSingleton('adminhtml/session')->getTeachingsupportData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTeachingsupportData());
          Mage::getSingleton('adminhtml/session')->setTeachingsupportData(null);
      } elseif ( Mage::registry('teachingsupport_data') ) {
          $form->setValues(Mage::registry('teachingsupport_data')->getData());
      }
      return parent::_prepareForm();
  }
}