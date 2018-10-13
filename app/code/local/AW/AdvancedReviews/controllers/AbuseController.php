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


class AW_AdvancedReviews_AbuseController extends Mage_Core_Controller_Front_Action {

    public function reportAction() {
        if ($this->getRequest()->getParam('real') && $reviewId = $this->getRequest()->getParam('reviewId')) {

            $session = Mage::getSingleton('core/session');
            $session->addSuccess($this->__('Thank you for report'));

            if (Mage::helper('advancedreviews')->isUserLogged()) {
                $customerId = Mage::getSingleton('customer/session')->getId();
                Mage::helper('advancedreviews')->addAbuse($reviewId, $customerId);
            } else {
                Mage::helper('advancedreviews')->addAbuse($reviewId);
                Mage::helper('advancedreviews')->markAbused($reviewId);
            }
        }
        $this->getRequest()->setParam('referer_url', Mage::helper('advancedreviews')->getReviewsBackUrl());
        $this->_redirectReferer(Mage::helper('advancedreviews')->getReviewsBackUrl());
    }

    public function reportajaxAction() {
        if ($reviewId = $this->getRequest()->getParam('reviewId')) {
            if (Mage::helper('advancedreviews')->isUserLogged()) {
                $customerId = Mage::getSingleton('customer/session')->getId();
                Mage::helper('advancedreviews')->addAbuse($reviewId, $customerId);
            } else {
                Mage::helper('advancedreviews')->addAbuse($reviewId);
                Mage::helper('advancedreviews')->markAbused($reviewId);
            }
            return $this->getResponse()->setBody(Zend_Json::encode(array('type' => 'success', 'message' => $this->__('Thank you for report'))));
        }
    }

}
