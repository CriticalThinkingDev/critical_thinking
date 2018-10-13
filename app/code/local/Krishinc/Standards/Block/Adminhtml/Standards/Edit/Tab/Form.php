<?php

class Krishinc_Standards_Block_Adminhtml_Standards_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $model = Mage::getModel('standards/standards');
     
      $fieldset = $form->addFieldset('standards_form', array('legend'=>Mage::helper('standards')->__('Standard Correlation Information')));
           
 	  $fieldset->addField('product_id', 'select', array(
          'label'     => Mage::helper('standards')->__('Product'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'product_id',
		  'values'    => $model->getProductIdOptionArray(),
      ));
	  $fieldset->addField('state', 'text', array(
          'label'     => Mage::helper('standards')->__('State'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'state'
      ));
	  
	  $fieldset->addField('standard', 'textarea', array(
          'label'     => Mage::helper('standards')->__('Standard'),
          'title'     => Mage::helper('standards')->__('Standard'),
          'name'      => 'standard',
		  'style'     => 'width:98%; height:50px;',
          'required'  => true,
      ));
	  $fieldset->addField('benchmark', 'editor', array(
          'label'     => Mage::helper('standards')->__('Benchmark'),
		  'title'     => Mage::helper('standards')->__('Benchmark'),
          'name'      => 'benchmark',
		  'style'     => 'width:98%; height:200px;',
          'wysiwyg'   => true,
          'required'  => false,
      ));
	  $fieldset->addField('page_numbers', 'textarea', array(
          'label'     => Mage::helper('standards')->__('Page Numbers'),
          'title'     => Mage::helper('standards')->__('Page Numbers'),
          'name'      => 'page_numbers',
		  'style'     => 'width:100%; height:80px;',
          'required'  => false,
      ));
      
      /* $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('standards')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('standards')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('standards')->__('Disabled'),
              ),
          ),
      )); */
     
      if ( Mage::getSingleton('adminhtml/session')->getStandardsData() )
        {
			/* editor settings */
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStandardsData());
            Mage::getSingleton('adminhtml/session')->setStandardsData(null);
			
			$wysiwygConfig["files_browser_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index');
			$wysiwygConfig["directives_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
			$wysiwygConfig["directives_url_quoted"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
			$wysiwygConfig["widget_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index');
			$wysiwygConfig["files_browser_window_width"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width');
			$wysiwygConfig["files_browser_window_height"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height');
			$plugins = $wysiwygConfig->getData("plugins");
			$plugins[0]["options"]["url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin');
			$plugins[0]["options"]["onclick"]["subject"] = "MagentovariablePlugin.loadChooser('".Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin')."', '{{html_id}}');";
			$plugins = $wysiwygConfig->setData("plugins",$plugins);
			
			$fieldset->addField('benchmark', 'editor', array(
			'name' => 'benchmark',
			'label' => Mage::helper('standards')->__('Content'),
			'title' => Mage::helper('standards')->__('Content'),
			'style' => 'width:700px; height:300px;',
			'wysiwyg' => true,
			'required' => false,
			'state' => 'html',
			'config' => $wysiwygConfig,
			));

			
        } elseif ( Mage::registry('standards_data') ) {
            $form->setValues(Mage::registry('standards_data')->getData());
        }
      
      return parent::_prepareForm();
  }
}