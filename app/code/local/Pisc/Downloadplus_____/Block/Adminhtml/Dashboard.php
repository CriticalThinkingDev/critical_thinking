<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Widget_Container
{
	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->_controller = 'adminhtml_dashboard';
	    $this->_blockGroup = 'downloadplus';
	    $this->_headerText = Mage::helper('downloadplus')->__('Download Dashboard');

        $this->_addButton('log', array(
            'label'     => Mage::helper('downloadplus')->__('Download Log'),
            'onclick'   => 'window.location.href=\'' . $this->getUrl('adminhtml/log/') . '\'',
            'class'     => 'edit'
        ));

	    parent::__construct();
	}

	/*
	 * Create Layout
	 */
    protected function _prepareLayout()
    {
    	$this->setChild('downloads_total',
                $this->getLayout()->createBlock('downloadplus/adminhtml_dashboard_downloads')
        );

    	$this->setChild('downloads_top_products',
                $this->getLayout()->createBlock('downloadplus/adminhtml_dashboard_products_top')
        );

    	$this->setChild('downloads_top_samples',
                $this->getLayout()->createBlock('downloadplus/adminhtml_dashboard_samples_top')
        );

        parent::_prepareLayout();
    }


}
