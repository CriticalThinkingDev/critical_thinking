<?php

class Krishinc_Teachingsupport_Block_Adminhtml_Teachingsupport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'teachingsupport';
        $this->_controller = 'adminhtml_teachingsupport';
        
        $this->_updateButton('save', 'label', Mage::helper('teachingsupport')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('teachingsupport')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('teachingsupport_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'teachingsupport_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'teachingsupport_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('teachingsupport_data') && Mage::registry('teachingsupport_data')->getId() ) {
        	$title = Mage::registry('teachingsupport_data')->getTitle(); 
        	return Mage::helper('teachingsupport')->__("Edit Item '%s'", $this->htmlEscape($title));
        } else {
            return Mage::helper('teachingsupport')->__('Add Item');
        }
    }
}