<?php
class Krishinc_Listrak_Block_Adminhtml_System_Config_Form_Field_Mapfields extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	 public function __construct()
    {
        $this->addColumn('magento', array(
            'label' => Mage::helper('listrak')->__('Fields'),
            'style' => 'width:120px',
        ));
        $this->addColumn('listrak_ids', array( 
            'label' => Mage::helper('listrak')->__('Listrak Field ID'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('listrak')->__('Add field');
        parent::__construct();
    }
}