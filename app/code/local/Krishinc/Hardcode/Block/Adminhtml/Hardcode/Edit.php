<?php

class Krishinc_Hardcode_Block_Adminhtml_Hardcode_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'hardcode';
        $this->_controller = 'adminhtml_hardcode';
        
        $this->_updateButton('save', 'label', Mage::helper('hardcode')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('hardcode')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('hardcode_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'hardcode_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'hardcode_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('hardcode_data') && Mage::registry('hardcode_data')->getId() ) {
            return Mage::helper('hardcode')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('hardcode_data')->getTitle()));
        } else {
            return Mage::helper('hardcode')->__('Add Item');
        }
    }
}