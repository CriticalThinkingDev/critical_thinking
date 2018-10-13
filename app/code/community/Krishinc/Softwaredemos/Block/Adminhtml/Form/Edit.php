<?php

class Krishinc_Softwaredemos_Block_Adminhtml_Form_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'softwaredemos';
        $this->_controller = 'adminhtml_form';
        $this->_headerText = Mage::helper('softwaredemos')->__('Softwaredemo Edit');
        $this->_updateButton('save', 'label', Mage::helper('softwaredemos')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('softwaredemos')->__('Delete Item'));
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		$this->_formScripts[] = "  
			function saveAndContinueEdit(){
			    editForm.submit($('edit_form').action+'back/edit/');
			}
		";
    } 

}