<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->_controller = 'adminhtml_log';
	    $this->_blockGroup = 'downloadplus';
	    $this->_headerText = Mage::helper('downloadplus')->__('Download Log');

        $this->_addButton('dashboard', array(
            'label'     => Mage::helper('downloadplus')->__('Download Dashboard'),
            'onclick'   => 'window.location.href=\'' . $this->getUrl('adminhtml/downloaddashboard/') . '\'',
            'class'     => 'edit'
        ));

	    parent::__construct();

	    $this->_removeButton('add');
	}

}
