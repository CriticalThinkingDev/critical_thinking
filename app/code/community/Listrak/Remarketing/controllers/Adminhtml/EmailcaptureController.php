<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.0.0
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2011 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

class Listrak_Remarketing_Adminhtml_EmailCaptureController
    extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('remarketing')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Fields Manager'),
                Mage::helper('adminhtml')->__('Field Manager')
            );
        return $this;
    }

    public function indexAction()
    {
        try {
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('remarketing/adminhtml_emailcapture'));
            $this->renderLayout();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setEmailCaptureData($this->getRequest()->getPost());
            $this->_redirect('adminhtml/dashboard');
            return;
        }

    }

    public function editAction()
    {
        try {
            $emailcaptureId = $this->getRequest()->getParam('id');
            $emailcaptureModel = Mage::getModel('listrak/emailcapture')->load($emailcaptureId);

            if ($emailcaptureModel->getId() || $emailcaptureId == 0) {

                Mage::register('emailcapture_data', $emailcaptureModel);

                $this->loadLayout();
                $this->_setActiveMenu('emailcapture');

                $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item Manager'),
                    Mage::helper('adminhtml')->__('Item Manager')
                );
                $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item News'),
                    Mage::helper('adminhtml')->__('Item News')
                );

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                $this->_addContent($this->getLayout()->createBlock('remarketing/adminhtml_emailcapture_edit'))
                    ->_addLeft($this->getLayout()->createBlock('remarketing/adminhtml_emailcapture_edit_tabs'));

                $this->renderLayout();
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('remarketing')->__('Item does not exist')
                );
                $this->_redirect('*/*/');
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setEmailCaptureData($this->getRequest()->getPost());
            $this->_redirect('*/*/index', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                $emailcaptureModel = Mage::getModel('listrak/emailcapture');

                $emailcaptureModel->setId($this->getRequest()->getParam('id'))
                    ->setPage($postData['page'])
                    ->setFieldId($postData['field_id'])
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setEmailCaptureData(false);

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setEmailCaptureData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $emailcaptureModel = Mage::getModel('listrak/emailcapture');

                $emailcaptureModel->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('importedit/adminhtml_emailcapture_grid')->toHtml()
        );
    }
}