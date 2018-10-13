<?php
/**
 * Downloadable Admin Download Statistics controller
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('downloadplusadmin/dashboard')
			->_addBreadcrumb(Mage::helper('downloadplus')->__('Downloads Dashboard'), Mage::helper('downloadplus')->__('Downloads Dashboard'));

		return $this;
	}

	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

}