<?php

class Krishinc_Standards_Block_Adminhtml_Standards_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'standards';
        $this->_controller = 'adminhtml_standards';
        
        $this->_updateButton('save', 'label', Mage::helper('standards')->__('Save Standard'));
        $this->_updateButton('delete', 'label', Mage::helper('standards')->__('Delete Standard'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('standards_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'standards_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'standards_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('standards_data') && Mage::registry('standards_data')->getId() ) {
            return Mage::helper('standards')->__("Edit Standard");
        } else {
            return Mage::helper('standards')->__('Add Standards');
        }
    }
	protected function _prepareLayout()
	{
		// Load Wysiwyg on demand and Prepare layout
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
			$block->setCanLoadTinyMce(true);
		}
		parent::_prepareLayout();
	}
}