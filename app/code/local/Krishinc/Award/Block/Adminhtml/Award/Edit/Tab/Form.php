<?php
    class Krishinc_Award_Block_Adminhtml_Award_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    {
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('Award_form', array('legend'=>Mage::helper('award')->__('Award information')));     

            $fieldset->addField('name', 'text', array(
	            'label' => Mage::helper('award')->__('Award Name'),
	            'class' => 'required-entry',
	            'required' => true,
	            'name' => 'name',
	        )); 
            $fieldset->addField('award_url', 'text', array(
	            'label' => Mage::helper('award')->__('Link'),
	          //  'class' => 'required-entry',
	            //'required' => true,
	            'name' => 'award_url',
	            'note'	=> 'Add url with http://',
	        )); 
	        
	        $outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
			$fieldset->addField('awarddate', 'date', array(
				'name' => 'awarddate',
				'label' =>  Mage::helper('award')->__('Date'),
				'image' => $this->getSkinUrl('images/grid-cal.gif'),
				'format' => $outputFormat, 
				'time' => false,
				//'style' => 'width: 140px;'
			)); 
			
	        $fieldset->addField('image', 'image', array(
	            'label' => Mage::helper('award')->__('Award Image'),
	            'name' => 'image',
	            'note'	=> 'Allowed file types are only image files (eg.: .jpg,.jpeg,.gif etc)',
	        )); 
              $fieldset->addField('is_companyaward', 'select', array(
	          'label'     => Mage::helper('award')->__('Company Award'),
	          'name'      => 'is_companyaward',
	          'values'    => array(
	              array(
	                  'value'     => 1,
	                  'label'     => Mage::helper('award')->__('Yes'),
	              ),
	
	              array(
	                  'value'     => 2,
	                  'label'     => Mage::helper('award')->__('No'),
	              ),
	          ),
	      ));
//	        $fieldset->addField('description', 'editor', array(
//                'name'      => 'description',
//                'label'     => Mage::helper('award')->__('Description'),
//                'title'     => Mage::helper('award')->__('Description'),
//                'style'     => 'width:100%; height:200px;',
//                'wysiwyg'   => false,
//                
//            )); 
			
	       $fieldset->addField('award_option_id', 'hidden', array(
                    'name'      => 'award_option_id',
                    'no_span'   => true,
                    'value'		=> Mage::registry('award_data')->getData('award_option_id')
                )
        	);

            if ( Mage::getSingleton('adminhtml/session')->getAwardData() )
            {
            	
                $form->setValues(Mage::getSingleton('adminhtml/session')->getAwardData());
                Mage::getSingleton('adminhtml/session')->setAwardData(null);
            } elseif ( Mage::registry('award_data') ) { 
                $form->setValues(Mage::registry('award_data')->getData());
            }
            return parent::_prepareForm();
        }
        protected function getAllManu()
        {
          $product = Mage::getModel('catalog/product');
          $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                      ->setEntityTypeFilter($product->getResource()->getTypeId())
                      ->addFieldToFilter('attribute_code', 'award'); //can be changed to any attribute
          $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
          $award = $attribute->getSource()->getAllOptions(false);
         
          return $award;
        }
        
    }