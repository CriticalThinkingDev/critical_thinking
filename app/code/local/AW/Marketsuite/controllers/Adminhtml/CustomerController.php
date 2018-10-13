<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    1.2.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Marketsuite_Adminhtml_CustomerController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('marketsuite/customers');
    }

    public function indexAction()
    {
        if (version_compare(Mage::getVersion(), '1.4.0.0', '>='))
            $this->_title($this->__('MSS'))->_title($this->__('Customers'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this
            ->loadLayout()
            ->_setActiveMenu('marketsuite')
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('marketsuite/adminhtml_customer_grid')->toHtml());
    }

    public function viewcustomerAction()
    {
        Mage::helper('marketsuite')->setBackUrl($this->getUrl('*/*/index'));
        return $this->_redirect(
            'adminhtml/customer/edit',
            array(
                'id' => $this->getRequest()->getParam('id'),
                AW_Marketsuite_Helper_Data::USE_AW_BACKURL_FLAG => 1
            )
        );
    }

    public function exportCsvAction()
    {
        $fileName = 'customers.csv';
        $content = $this->getLayout()->createBlock('marketsuite/adminhtml_customer_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'customers.xml';
        $content = $this->getLayout()->createBlock('marketsuite/adminhtml_customer_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}
