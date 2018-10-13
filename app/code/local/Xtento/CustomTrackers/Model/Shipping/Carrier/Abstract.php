<?php

/**
 * Product:       Xtento_CustomTrackers (1.6.4)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:07:13+00:00
 * Last Modified: 2013-09-06T12:02:44+02:00
 * File:          app/code/local/Xtento/CustomTrackers/Model/Shipping/Carrier/Abstract.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_CustomTrackers_Model_Shipping_Carrier_Abstract extends Mage_Shipping_Model_Carrier_Abstract
{
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        // Not used for shipping.. so just return an empty result.
        return Mage::getModel('shipping/rate_result');
    }

    public function getConfigData($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'customtrackers/' . $this->_code . '/' . $field;
        return Mage::getStoreConfig($path, $this->getStore());
    }

    public function getConfigFlag($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'customtrackers/' . $this->_code . '/' . $field;
        return Mage::getStoreConfigFlag($path, $this->getStore());
    }

    public function isTrackingAvailable()
    {
        if (!Mage::helper('customtrackers/data')->getModuleEnabled()) {
            return false;
        }
        if (!$this->isActive()) {
            return false;
        }
        return true;
    }

    /*
     * Taken from Mage_Usa_Model_Shipping_Carrier_Abstract
     */
    public function getTrackingInfo($tracking)
    {
        $result = $this->getTracking($tracking);

        if ($result instanceof Mage_Shipping_Model_Tracking_Result) {
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        } elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

    /*
     * Based on Mage_Usa_Model_Shipping_Carrier_Dhl
     */
    public function getTracking($trackings)
    {
        $r = new Varien_Object();
        $id = $this->getConfigData('id');
        $r->setId($id);
        $this->_rawTrackRequest = $r;

        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }
        $this->_getCgiTracking($trackings);

        return $this->_result;
    }

    /*
     * Based on Mage_Usa_Model_Shipping_Carrier_Ups
     */
    protected function _getCgiTracking($trackings)
    {
        $result = Mage::getModel('shipping/tracking_result');
        //$defaults = $this->getDefaults();
        foreach ($trackings as $trackingNumber) {
            // Get variables:
            $shippingPostcode = Mage::helper('customtrackers')->getShippingPostcode();
            $shipmentDate = new Zend_Date();
            $orderIncrementId = '';
            $firstname = '';
            $lastname = '';
            $countryCode = '';
            if (Mage::registry('xt_current_shipment')) {
                $shipment = Mage::registry('xt_current_shipment');
                if ($shipment->getCreatedAt()) {
                    $shipmentDate = new Zend_Date();
                    $shipmentDate->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
                    $shipmentDate->set($shipment->getCreatedAt(), Varien_Date::DATETIME_INTERNAL_FORMAT);
                    $shipmentDate->setLocale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
                    $shipmentDate->setTimezone(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE));
                }
                $realShipment = Mage::getModel('sales/order_shipment')->load($shipment->getParentId());
                if ($realShipment->getId()) {
                    if ($realShipment->getOrder()) {
                        $orderIncrementId = $realShipment->getOrder()->getIncrementId();
                        if ($shipAddress = $realShipment->getOrder()->getShippingAddress()) {
                            $firstname = $shipAddress->getFirstname();
                            $lastname = $shipAddress->getLastname();
                            $countryCode = $shipAddress->getCountryId();
                        }
                    }
                }
                Mage::unregister('xt_current_shipment');
            }
            // Tracking result
            $status = Mage::getModel('shipping/tracking_result_status');
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setCarrier('tracker');
            $status->setTracking($trackingNumber);
            $status->setPopup(true);
            $status->setUrl(preg_replace(array("/#TRACKINGNUMBER#/i", "/#ZIP#/i", "/#d#/i", "/#m#/i", "/#y#/", "/#Y#/", "/#ORDERNUMBER#/i", "/#FIRSTNAME#/i", "/#LASTNAME#/i", "/#COUNTRYCODE#/i"), array(urlencode($trackingNumber), urlencode($shippingPostcode), $shipmentDate->get('dd'), $shipmentDate->get('MM'), $shipmentDate->get('yy'), $shipmentDate->get('y'), urlencode($orderIncrementId), urlencode($firstname), urlencode($lastname), urlencode($countryCode)), $this->getConfigData('url')));
            $result->append($status);
        }

        $this->_result = $result;
        return $result;
    }
}