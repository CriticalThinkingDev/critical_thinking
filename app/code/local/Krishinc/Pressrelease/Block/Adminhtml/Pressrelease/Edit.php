<?php

class Krishinc_Pressrelease_Block_Adminhtml_Pressrelease_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'pressrelease';
        $this->_controller = 'adminhtml_pressrelease';
        
        $this->_updateButton('save', 'label', Mage::helper('pressrelease')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('pressrelease')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('pressrelease_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'pressrelease_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'pressrelease_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('pressrelease_data') && Mage::registry('pressrelease_data')->getId() ) {
            return Mage::helper('pressrelease')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('pressrelease_data')->getTitle()));
        } else {
            return Mage::helper('pressrelease')->__('Add Item');
        }
    }
}