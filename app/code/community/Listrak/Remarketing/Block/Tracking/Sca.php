<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.1.9
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2014 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

class Listrak_Remarketing_Block_Tracking_Sca extends Listrak_Remarketing_Block_Require_Sca
{
    private $_initialized = false;
    private $_convertSession = null;

    public function _toHtml() {
        try {
            if (!$this->canRender())
                return '';

            if ($this->hasAjaxScript()) {
                $this->addLine("document.observe('dom:loaded', function() { Listrak_Remarketing.track(); });");
            }
            else {
                $this->addCustomerJS();
                if (!$this->addCartJS())
                    $this->addLine("_ltk.SCA.Submit();");
            }

            return parent::_toHtml();
        } catch(Exception $e) {
            Mage::getModel('listrak/log')->addException($e);
            return '';
        }
    }

    public function getCartJavascript() {
        $this->_ensureLoaded();

        $noSubmit = $this->addCartJS(true);
        if (trim($this->getScript()) && !$noSubmit)
            $this->addLine("_ltk.SCA.Submit();");

        return $this->getScript(false);
    }

    private $_canRender = null;
    public function canRender() {
        $this->_ensureLoaded();
        if ($this->_canRender == null)
            $this->_canRender = parent::canRender() && !$this->isOrderConfirmationPage() && ($this->hasAjaxScript() || $this->hasCartJS() || $this->hasCustomerJS());

        return $this->_canRender;
    }

    private function hasAjaxScript() {
        return $this->getFullPageRendering() && Mage::helper('remarketing')->ajaxTracking();
    }

    private function hasSessionToConvert() {
        return $this->_convertSession != null && $this->_convertSession->getId() && !$this->_convertSession->getConverted();
    }

    private function hasCartJS() {
        return $this->hasSessionToConvert()
            || Mage::getSingleton('checkout/session')->getListrakCartModified()
            || $this->isCartPage();
    }

    private function addCartJS($forceRender = false) {
        $noSubmit = false;

        if ($forceRender || $this->hasCartJS()) {
            if ($this->hasSessionToConvert()) {
                $this->addLine("_ltk.SCA.SetSessionID({$this->toJsString($this->_convertSession->getSessionId())});");

                $emails = $this->_convertSession->getEmails();
                if (count($emails) > 0 && !Mage::getSingleton('customer/session')->isLoggedIn())
                    $this->addLine("_ltk.SCA.SetCustomer({$this->toJsString($emails[0]['email'])}, '', '');");
            }

            $chkSession = Mage::getSingleton('checkout/session');

            $this->addLine("_ltk.SCA.Stage = 1;");

            if (Mage::getSingleton('checkout/cart')->getSummaryQty() > 0) {
                foreach($this->getCartItems() as $item) {
                    $this->addLine("_ltk.SCA.AddItemWithLinks("
                            . $this->toJsString($item->getSku()) . ", "
                            . $this->toJsString($item->getQty()) . ", "
                            . $this->toJsString($item->getPrice()) . ", "
                            . $this->toJsString($item->getName()) . ", "
                            . $this->toJsString($item->getImageUrl()) . ", "
                            . $this->toJsString($item->getProductUrl()) . ");");
                }

                $ltksid = $this->getBasketId();
                $this->addLine("_ltk.SCA.Meta1 = {$this->toJsString($ltksid)};");
                $chkSession->setCartLtksid($ltksid);
            }
            else {
                $this->addLine("_ltk.SCA.ClearCart();");
                $noSubmit = true;
                // _ltk.SCA.Submit is called by _ltk.SCA.ClearCart
            }

            $chkSession->unsListrakCartModified();

            if ($this->hasSessionToConvert()) {
                $this->_convertSession->setConverted(true);
                $this->_convertSession->save();
                $this->_convertSession->deleteCookie();
            }
        }

        return $noSubmit;
    }

    private function hasCustomerJS() {
        $custSession = Mage::getSingleton('customer/session');
        return $custSession->isLoggedIn() && !$custSession->getListrakCustomerTracked();
    }

    private function addCustomerJS() {
        if ($this->hasCustomerJS()) {
            $custSession = Mage::getSingleton('customer/session');
            $cust = $custSession->getCustomer();

            $this->addLine("_ltk.SCA.SetCustomer("
                . $this->toJsString($cust->getEmail()) . ", "
                . $this->toJsString($cust->getFirstname()) . ", "
                . $this->toJsString($cust->getLastname()) . ");");

            $custSession->setListrakCustomerTracked(true);
        }
    }

    private function getCartItems() {
        $result = array();

        $productHelper = Mage::helper('remarketing/product');
        foreach (Mage::getSingleton('checkout/cart')->getQuote()->getAllVisibleItems() as $item) {
            $info = $productHelper->getProductInformationFromQuoteItem($item, array('product_url', 'image_url'));

            $item->setSku($info->getSku());
            $item->setProductUrl($info->getProductUrl());
            $item->setImageUrl($info->getImageUrl());

            $result[] = $item;
        }

        return $result;
    }

    private function getBasketId() {
        $storeId = Mage::app()->getStore()->getStoreId();
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();

        $str = $storeId . ' ' . $quoteId;
        while(strlen($str) < 16) // 5 for store ID, 1 for the space, and 10 for the quote ID
            $str .= ' ' . $quoteId;
        $str = substr($str, 0, 16);

        return Mage::helper('remarketing')->urlEncrypt($str);
    }

    private function _ensureLoaded() {
        if (!$this->_initialized) {
            if (Mage::helper('remarketing')->trackingTablesExist())
                $this->_convertSession = Mage::getSingleton('listrak/session')->loadFromCookie();

            if ($this->isOrderConfirmationPage())
                Mage::getSingleton('customer/session')->unsListrakCustomerTracked();

            $this->_initialized = true;
        }
    }
}
