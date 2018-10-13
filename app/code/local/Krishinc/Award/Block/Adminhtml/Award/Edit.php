<?php
    class Krishinc_Award_Block_Adminhtml_Award_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
    {
        public function __construct()
        {
            parent::__construct();
                   
            $this->_objectId = 'id';
            $this->_blockGroup = 'award';
            $this->_controller = 'adminhtml_award';
     
            $this->_updateButton('save', 'label', Mage::helper('award')->__('Save Award'));
            $this->_updateButton('delete', 'label', Mage::helper('award')->__('Delete Award'));
        }
     
        public function getHeaderText()
        {
            if( Mage::registry('award_data') && Mage::registry('award_data')->getId() ) {
                     
                return Mage::helper('award')->__("Edit Award '%s'", $this->htmlEscape(Mage::registry('award_data')->getName()));
            } else {
                return Mage::helper('award')->__('Update Award Information');
            }
        }
    }