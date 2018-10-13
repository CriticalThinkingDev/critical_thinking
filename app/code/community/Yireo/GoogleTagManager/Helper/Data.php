<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 */

class Yireo_GoogleTagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigValue($key = null, $default_value = null)
    {
        $value = Mage::getStoreConfig('googletagmanager/settings/'.$key);
        if(empty($value)) $value = $default_value;
        return $value;
    }

    /*
     * Usage: 
     *   echo Mage::helper('googletagmanager')->getHtml($arguments);
     *   $arguments is an associative array (size, count, url)
    
     */
    public function getHeaderScript()
    {
        $html = '';

        // Check for the frontend layout
        if (!($layout = Mage::app()->getFrontController()->getAction()->getLayout())) {
            return $html;
        }

        // Check for the Google Tag Manager block
        if (!($block = $layout->getBlock('googletagmanager'))) {
            return $html;
        }
 
        // Add order-information
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $actionName = Mage::app()->getRequest()->getActionName();
        $lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if($controllerName == 'onepage' && $actionName == 'success' && !empty($lastOrderId)) {
                $order = Mage::getModel('sales/order')->load($lastOrderId);
                $orderBlock = $layout->getBlock('googletagmanager_order');
                $orderBlock->setOrder($order); 
                $html .= $orderBlock->toHtml();
         } else { 
//            $quote = Mage::getModel('checkout/cart')->getQuote();
//            if($quote->getId() > 0) {
                $quoteBlock = $layout->getBlock('googletagmanager_quote'); 
//                $quoteBlock->setQuote($quote);
                $html .= $quoteBlock->toHtml();
//            }
        }

        $html .= $block->toHtml();
        return $html;
    }
    /**
     * Get visitor data for use in the data layer.
     *
     * @link https://developers.google.com/tag-manager/reference
     * @return array
     */
    public function getVisitorData()
    {
        $data = array();
        $customer = Mage::getSingleton('customer/session');

        // visitorId
        if ($customer->getCustomerId()) $data['visitorId'] = (string)$customer->getCustomerId(); else $data['visitorId'] = 0;

        // visitorLoginState
        $data['visitorLoginState'] = ($customer->isLoggedIn()) ? 'Logged in' : 'Logged out';

        // visitorType
        $data['visitorType'] = (string)Mage::getModel('customer/group')->load($customer->getCustomerGroupId())->getCode();

        // visitorExistingCustomer / visitorLifetimeValue
        $orders = Mage::getResourceModel('sales/order_collection')->addFieldToSelect('*')->addFieldToFilter('customer_id',$customer->getId());
        $ordersTotal = 0;
        foreach ($orders as $order) {
            $ordersTotal += $order->getGrandTotal();
        }
        if ($customer->isLoggedIn()) {
            $data['visitorLifetimeValue'] = round($ordersTotal,2);
        } else {
            $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
             if(!empty($order)) { 
                $data['visitorLifetimeValue'] =  $order->getGrandTotal(); 
            } else {
                $data['visitorLifetimeValue'] = 'null';
            }
        }
        $data['visitorExistingCustomer'] = ($ordersTotal > 0) ? 'Yes' : 'No';
        $data['visitorLifetimeValue'] = ($data['visitorLifetimeValue'] > 0) ? $data['visitorLifetimeValue'] : '0';
        $visitorsString = '';
        $visitorsString = "'visitorId':".$data['visitorId'].",".
        "'visitorLoginState':'".$data['visitorLoginState']."',".
        "'visitorType':'".$data['visitorType']."',".
        "'visitorLifetimeValue':".$data['visitorLifetimeValue'].",".
        "'visitorExistingCustomer':'".$data['visitorExistingCustomer']."'";        
    
        return $visitorsString;  
    } 
}
