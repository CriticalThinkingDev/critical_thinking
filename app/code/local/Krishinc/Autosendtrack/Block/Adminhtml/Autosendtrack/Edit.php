<?php

class Krishinc_Autosendtrack_Block_Adminhtml_Autosendtrack_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'autosendtrack';
        $this->_controller = 'adminhtml_autosendtrack';
        
        $this->_updateButton('save', 'label', Mage::helper('autosendtrack')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('autosendtrack')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('autosendtrack_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'autosendtrack_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'autosendtrack_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('autosendtrack_data') && Mage::registry('autosendtrack_data')->getId() ) {
            return Mage::helper('autosendtrack')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('autosendtrack_data')->getTitle()));
        } else {
            return Mage::helper('autosendtrack')->__('Add Item');
        }
    }
}