<?php
/**
 * Downloadable Admin Downloads controller
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Adminhtml_LogController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('downloadplus/dashboard')
			->_addBreadcrumb(Mage::helper('downloadplus')->__('Downloads Log'), Mage::helper('downloadplus')->__('Downloads Log'));

		return $this;
	}

	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

}