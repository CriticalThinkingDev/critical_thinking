<?php

class Krishinc_Educent_Block_Adminhtml_Educent_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'educent';
        $this->_controller = 'adminhtml_educent';
        
        $this->_updateButton('save', 'label', Mage::helper('educent')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('educent')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('educent_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'educent_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'educent_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('educent_data') && Mage::registry('educent_data')->getId() ) {
            return Mage::helper('educent')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('educent_data')->getTitle()));
        } else {
            return Mage::helper('educent')->__('Add Item');
        }
    }
}