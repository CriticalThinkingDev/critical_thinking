<?php

class Krishinc_Softwaredemos_Block_Adminhtml_Form_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Preparing form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        /*Subject Id*/
	        $product = Mage::getModel('catalog/product');
	        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
	            ->setEntityTypeFilter($product->getResource()->getTypeId())
	            ->addFieldToFilter('attribute_code', 'subject');
	        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
	        $subject = array('-1'=>'Select Subject');
	        $subjectcollection = $attribute->getSource()->getAllOptions(false);
	        foreach ($subjectcollection as $subjectcoll){
	            $subject[$subjectcoll['value']]= $subjectcoll['label'];
	        }
        /**/ 
        $fieldset = $form->addFieldset('softwaredemos_form', array('legend'=>Mage::helper('softwaredemos')->__('Software Demos information')));

        $fieldset->addField('softname', 'text', array(
            'name'      => 'softname',
            'label'     => Mage::helper('softwaredemos')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,  
        ));
 
        $fieldset->addField('subject_id', 'select', array(
            'name'  => 'subject_id',
            'label' => Mage::helper('softwaredemos')->__('Select Subject'),
            'id'    => 'subject_id',
            'title' => Mage::helper('softwaredemos')->__('Select Subject'),
            'class' => 'input-select select required-entry ',
            'required' => true,
            'values' => $subject,
        ));

       $fieldset->addField('thumbline_img', 'image', array(
	        'label'     => Mage::helper('softwaredemos')->__('Thumbline Image'),
	        'required'  => false,
	        'name'      => 'thumbline_img',
	    ));
        $fieldset->addField('description', 'editor', array(
            'label'     => Mage::helper('softwaredemos')->__('Description'),
            'class'     => 'required-entry',
            'name'      => 'description',
        ));

        $fieldset->addField('icon_img', 'image', array(
            'label'     => Mage::helper('softwaredemos')->__('Icon'),
            'name'      => 'icon_img',
        ));
  
 
        $fieldset->addField('large_img', 'image', array(
            'label'     => Mage::helper('softwaredemos')->__('Base Image'),
            'required'  => false,
            'name'      => 'large_img',
        )); 
        
 		$fieldset->addField('type', 'select', array(
            'label'     => Mage::helper('softwaredemos')->__('Software Type'),
            'name'      => 'type',
            'values'    => array(
                array(
                    'value'     => 'download',
                    'label'     => Mage::helper('softwaredemos')->__('Download Demo'),
                ),

                array(
                    'value'     => 'play',
                    'label'     => Mage::helper('softwaredemos')->__('Play Online Demo'),
                ),
            ),
        ));
  		 $fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => Mage::helper('softwaredemos')->__('Sort Order'),
            'class'     => 'required-entry',
            'required'  => true,  
        ));
        
          $attributeArray = array();
       $product = Mage::getModel("catalog/product");
       $attributes = Mage::getResourceModel("eav/entity_attribute_collection") 
                          ->setEntityTypeFilter($product->getResource()->getTypeId())
                          ->addFieldToFilter("attribute_code", "grade") // This can be changed to any attribute code 
                          ->load(false); 
                 
             $attribute = $attributes->getFirstItem()->setEntity($product->getResource()); /* @var $attribute Mage_Eav_Model_Entity_Attribute */ 
                    
             $sortby = $attribute->getSource()->getAllOptions(false);
         //    $attributeArray[0] = $this->__('Select Grades');
         
             foreach($sortby as $sort_attribute) {
             // here i m getting only "lable" and "value" as array index of $sort_attribute;         
             $attributeArray[] = array('value' => $sort_attribute['value'], 'label'=> $sort_attribute["label"]); 
             }
        
      

      $fieldset->addField('grades', 'multiselect', array(
          'label'     => Mage::helper('softwaredemos')->__('Grades'),
          'name'      => 'grades',          
          'values'    => $attributeArray, 
          'required'  => true,  
         
      ));
        
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('softwaredemos')->__('Status'),
            'name'      => 'status_form', //Change name from status to status_form to remove the conflict of product grid search status and software demo form status,
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('softwaredemos')->__('Enabled'),
                ),

                array(
                    'value'     => 2,
                    'label'     => Mage::helper('softwaredemos')->__('Disabled'),
                ),
            ),
        ));

        if (Mage::getSingleton('adminhtml/session')->getSoftwaredemosData())
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSoftwaredemosData());
            Mage::getSingleton('adminhtml/session')->setSoftwaredemosData(null);
            
        } elseif ( Mage::registry('softwaredemos_data') ) {
        	
            $form->setValues(Mage::registry('softwaredemos_data')->getData());
        }
        return parent::_prepareForm();
    }

}