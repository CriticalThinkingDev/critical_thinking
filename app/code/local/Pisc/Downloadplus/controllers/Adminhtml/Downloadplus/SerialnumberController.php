<?php
/**
 * Downloadable Admin Download Serialnumber controller
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Adminhtml_Downloadplus_SerialnumberController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		return $this;
	}

	protected function getValue(&$value, $default)
	{
		if (isset($value)) {
			return $value;
		}
		return $default;
	}

	public function indexAction() {
	}

	public function assignedAction()
	{
		if (!Mage::getSingleton('admin/session')->isAllowed('sales/download_serialnumbers/download_serialnumbers_assigned')) {
			Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('downloadplus')->__('Currently you do not have the related Privileges to edit Serianumbers.'));
			$this->_redirect('adminhtml');
		} else {
			$this->loadLayout()
				->_setActiveMenu('downloadplus/serialnumber_assigned')
				->_addBreadcrumb(Mage::helper('downloadplus')->__('Serialnumbers assigned to Orders'), Mage::helper('downloadplus')->__('Serialnumbers assigned to Orders'));

			$this->_initAction()
				->renderLayout();
		}
	}

	public function importAction()
	{
		if (!Mage::getSingleton('admin/session')->isAllowed('downloadplus/catalog_product_edit_serialnumbers')) {
			Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('downloadplus')->__('Currently you do not have the related Privileges to edit Serianumbers.'));
			$this->_redirect('adminhtml');
		} else {
			$this->loadLayout()
				->_setActiveMenu('downloadplus/serialnumber_import')
				->_addBreadcrumb(Mage::helper('downloadplus')->__('Import storewide Serialnumbers'), Mage::helper('downloadplus')->__('Import storewide Serialnumbers'));

			$this->_initAction()
				->renderLayout();
		}
	}

	public function importPostAction()
	{
		if (!Mage::getSingleton('admin/session')->isAllowed('downloadplus/catalog_product_edit_serialnumbers')) {
			Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('downloadplus')->__('Currently you do not have the related Privileges to edit Serianumbers.'));
			$this->_redirect('adminhtml');
		} else {
			$data = $this->getRequest()->getPost('downloadplus');
			if ($serials = $this->getValue($data['serialnumbers'], false)) {
				$helper = Mage::helper('downloadplus');
				$serialPool = $this->getValue($data['serialnumberpool'], false);
				if ($serialPool) {
					try {
						$count = $helper->importSerialnumbers($serials, $serialPool, null);
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('downloadplus')->__('%s Serialnumbers imported.', $count));
					} catch (Exception $e) {
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
				} else {
					Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('downloadplus')->__('Please define or select a serialnumber pool to import into.', $count));
				}
			} else {
				Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('downloadplus')->__('No Serialnumbers found to import.'));
			}

			$this->_redirect('*/*/import');
		}
	}

	public function availableAction()
	{
		$this->loadLayout()
			->_setActiveMenu('downloadplus/serialnumber_available')
			->_addBreadcrumb(Mage::helper('downloadplus')->__('Available shared Serialnumbers'), Mage::helper('downloadplus')->__('Available shared Serialnumbers'));

		$this->_initAction()
			->renderLayout();
	}

	public function massRemoveAction()
	{
		if (!Mage::getSingleton('admin/session')->isAllowed('downloadplus/catalog_product_edit_serialnumbers')) {
			Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('downloadplus')->__('Currently you do not have the related Privileges to edit Serianumbers.'));
			$this->_redirect('adminhtml');
		} else {
			$ids = $this->getRequest()->getParam('serialnumber_ids');
			$count = 0;
			foreach ($ids as $id) {
				$serial = Mage::getModel('downloadplus/product_serialnumber')->load($id);
				if ($serial->getId()==$id) {
					$serial->delete();
					$count++;
				}
			}
			if ($count>0) {
				$this->_getSession()->addSuccess($this->__('%s Serialnumber(s) successfully removed', $count));
			}

			$this->_redirect('*/*/available');
		}
	}

}