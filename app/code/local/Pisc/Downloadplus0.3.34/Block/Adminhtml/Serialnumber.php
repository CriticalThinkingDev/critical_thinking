<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Block_Adminhtml_Serialnumber extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->_controller = 'adminhtml_serialnumber';
	    $this->_blockGroup = 'downloadplus';
	    $this->_headerText = Mage::helper('downloadplus')->__('Serialnumbers assigned to Orders');

	    parent::__construct();

	    $this->_removeButton('add');
	}

}
