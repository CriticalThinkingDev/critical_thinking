<?php
/**
 * Downloadable Admin Download Details controller
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Adminhtml_DetailController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('downloadplusadmin/details')
			->_addBreadcrumb(Mage::helper('downloadplus')->__('Downloads Log'), Mage::helper('downloadplus')->__('Downloads Log'));

		return $this;
	}

	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

}