<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Block_Adminhtml_Serialnumber_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->_mode = 'import';
		$this->_controller = 'adminhtml_serialnumber';
	    $this->_blockGroup = 'downloadplus';
	    $this->_headerText = Mage::helper('downloadplus')->__('Import storewide Serialnumbers');

	    $this->setData('form_action_url', $this->getUrl('adminhtml/serialnumber/importPost', Array('_secure'=>true)));
	     
	    parent::__construct();

	    $this->_removeButton('back');
	    $this->_removeButton('reset');
	    $this->_removeButton('save');
	    $this->_removeButton('add');
	    
	    $this->_addButton('available', array(
	    	'label'     => Mage::helper('downloadplus')->__('Available Serialnumbers'),
	    	'onclick'   => 'window.location.href=\'' . $this->getUrl('adminhtml/serialnumber/available/') . '\'',
	    	'class'     => ''
	    ));
	    
	    $this->_addButton('import', array(
	    	'label'     => Mage::helper('downloadplus')->__('Import Serialnumbers'),
	    	'onclick'   => 'editForm.submit();',
	    	'class'     => 'save',
	    ), 1);
	     
	}

}
