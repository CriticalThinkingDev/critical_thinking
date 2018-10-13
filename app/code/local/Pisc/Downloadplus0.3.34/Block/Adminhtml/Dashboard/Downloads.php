<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Block_Adminhtml_Dashboard_Downloads extends Mage_Adminhtml_Block_Dashboard_Bar
{

	protected function _construct()
    {
    	parent::_construct();
        $this->setTemplate('dashboard/salebar.phtml');
    }

    protected function _prepareLayout()
    {
    	$this->addTotal(
    			$this->__('Lifetime Downloads'),
    			Mage::getModel('downloadplus/log')->getDownloadTotal(),
    			true
    		);
    }

}