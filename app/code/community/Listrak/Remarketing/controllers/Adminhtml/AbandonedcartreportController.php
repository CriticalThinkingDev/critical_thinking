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

class Listrak_Remarketing_Adminhtml_AbandonedcartreportController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('remarketing');
        return $this;
    }

    public function indexAction()
    {
        try {
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('remarketing/adminhtml_abandonedcartreport'));
            $this->renderLayout();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setEmailCaptureData($this->getRequest()->getPost());
            $this->_redirect('adminhtml/dashboard');
            return;
        }

    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('importedit/adminhtml_abandonedcartreport_grid')->toHtml()
        );
    }
}