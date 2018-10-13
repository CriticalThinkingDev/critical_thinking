<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Block_Adminhtml_Serialnumber_Available extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->_controller = 'adminhtml_serialnumber_available';
	    $this->_blockGroup = 'downloadplus';
	    $this->_headerText = Mage::helper('downloadplus')->__('Available shared Serialnumbers');

	    parent::__construct();

	    $this->_removeButton('add');
	    
	    $this->_addButton('import', array(
	    	'label'     => Mage::helper('downloadplus')->__('Import shared Serialnumbers'),
	    	'onclick'   => 'window.location.href=\'' . $this->getUrl('adminhtml/serialnumber/import/') . '\'',
	    	'class'     => 'add'
	    ));
	     
	}

}
