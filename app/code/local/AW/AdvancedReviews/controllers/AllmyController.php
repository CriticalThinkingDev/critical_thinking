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


class AW_AdvancedReviews_AllmyController extends Mage_Core_Controller_Front_Action {

    protected $_customerId;

    protected function _initCustomer() {
        $this->setCustomerId();
        Mage::register('aw_ar_customer_id', $this->getCustomerId());
    }

    protected function _isValidCustomer() {
        $currentCustomer = Mage::getSingleton('customer/session')->getCustomer();
        return ($currentCustomer->getId() == $this->getCustomerId()) && Mage::getModel('review/review')->getCollection()->isValidCustomer($this->getCustomerId());
    }

    public function getCustomerId() {
        return $this->_customerId ? $this->_customerId : null;
    }

    public function setCustomerId() {
        return $this->_customerId = $this->getRequest()->getParam('customerId');
    }

    public function listAction() {
        $this->_initCustomer();

        /*
         * Customer who not have anyone review can make self url.
         * _isValidCustomer() validate it.
         */
        if ($this->setCustomerId() && $this->_isValidCustomer()) {
            $this->loadLayout();

            if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbsBlock->addCrumb('home', array(
                    'label' => 'Home',
                    'link' => Mage::getUrl(),
                    'readonly' => false,
                ));

                $breadcrumbsBlock->addCrumb('customername', array('label' => Mage::helper('advancedreviews')->__('Reviewed by') . " " .
                    Mage::helper('advancedreviews')->getCustomerNicknameById($this->getCustomerId())));
            }
            $this->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
        return;
    }

}
