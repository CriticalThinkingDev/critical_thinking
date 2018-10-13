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
 * @package    AW_AdvancedReviews
 * @version    2.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_AdvancedReviews_Adminhtml_AbuseController extends Mage_Adminhtml_Controller_Action {

    protected function _prepareInit() {
        $this->loadLayout()
                ->_setActiveMenu('catalog/reviews_ratings/advancedreviews/abused_reviews')
                ->_addBreadcrumb(Mage::helper('advancedreviews')->__('Advanced Reviews'), Mage::helper('advancedreviews')->__('Abuse reports'));

        if (method_exists($this, '_title')) {
            $this->_title($this->__('Advanced Reviews'))->_title(Mage::helper('advancedreviews')->__('Abuse reports'));
        }

        return $this;
    }

    public function indexAction() {
        $this->_prepareInit()->renderLayout();
    }

    public function deleteAbuseAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('advancedreviews/abuse');
                $model->setId($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')
                                ->__('Report was successfully deleted'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreviews/adminhtml_abuse_grid')->toHtml());
    }

}
