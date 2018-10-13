<?php

class Krishinc_Pressrelease_Block_Adminhtml_Pressrelease_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $model = Mage::getModel('pressrelease/pressrelease');
      $filename = '';
      if($id = Mage::app()->getRequest()->getParam('id'))
      {
      	$data =$model->load($id);
      	$filename = Mage::getBaseUrl('media')."pressrelease/".$data->getFilename();
      }
      $fieldset = $form->addFieldset('pressrelease_form', array('legend'=>Mage::helper('pressrelease')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('pressrelease')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      
 	  $fieldset->addField('dateline', 'text', array(
          'label'     => Mage::helper('pressrelease')->__('Date Line'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'dateline',
      ));
      
      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('pressrelease')->__('Pdf File'),
          'required'  => false,
          'name'      => 'filename',
          'after_element_html' => ($filename?'<br/><a href="'.$filename.'" target="_blank">'.$data->getFilename().'</a>':'<br/>Please upload only pdf file.'),
	  ));
	  
  	 $fieldset->addField('pagelink', 'text', array(
          'label'     => Mage::helper('pressrelease')->__('Pagelink'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'pagelink',
          'after_element_html' => '<br/>Please donot use special characters in Pagelink.'
      ));
	$outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG);
	$fieldset->addField('pressdate', 'date', array(
		'name' => 'pressdate',
		'label' =>  Mage::helper('pressrelease')->__('Press Release Date'),
		'image' => $this->getSkinUrl('images/grid-cal.gif'),
		'format' => $outputFormat,
		'time' => false,
		//'style' => 'width: 140px;'
	)); 
		 
	  
    /*  $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('pressrelease')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('pressrelease')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('pressrelease')->__('Disabled'),
              ),
          ),
      ));
     */
      
      if ( Mage::getSingleton('adminhtml/session')->getPressreleaseData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPressreleaseData());
          Mage::getSingleton('adminhtml/session')->setPressreleaseData(null);
      } elseif ( Mage::registry('pressrelease_data') ) {
          $form->setValues(Mage::registry('pressrelease_data')->getData());
      }
      return parent::_prepareForm();
  }
}