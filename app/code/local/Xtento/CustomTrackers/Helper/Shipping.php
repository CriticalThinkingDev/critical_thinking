<?php

/**
 * Product:       Xtento_CustomTrackers (1.6.4)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:07:13+00:00
 * Last Modified: 2014-05-09T17:01:26+02:00
 * File:          app/code/local/Xtento/CustomTrackers/Helper/Shipping.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_CustomTrackers_Helper_Shipping extends Mage_Shipping_Helper_Data
{
    /**
     * Shipping tracking popup URL getter
     *
     * @param Mage_Sales_Model_Abstract $model
     * @return string
     */
    public function getTrackingPopupUrlBySalesModel($model)
    {
        if (Mage::helper('customtrackers/data')->getModuleEnabled()) {
            if (Mage::getStoreConfigFlag('customtrackers/advanced/disable_direct_tracking') && Mage::app()->getStore()->isAdmin()) {
                return $this->_getBuiltInTrackingLink($model);
            }
            $directTrackingUrl = $this->_getDirectTrackingUrl($model);
            if ($directTrackingUrl) {
                return $directTrackingUrl;
            }
        }
        return $this->_getBuiltInTrackingLink($model);
    }

    private function _getBuiltInTrackingLink($model)
    {
        if ($model instanceof Mage_Sales_Model_Order) {
            return $this->_getTrackingUrl('order_id', $model);
        } elseif ($model instanceof Mage_Sales_Model_Order_Shipment) {
            return $this->_getTrackingUrl('ship_id', $model);
        } elseif ($model instanceof Mage_Sales_Model_Order_Shipment_Track) {
            return $this->_getTrackingUrl('track_id', $model, 'getEntityId');
        }
        return "";
    }

    private function _getDirectTrackingUrl($model)
    {
        $hash = false;
        if ($model instanceof Mage_Sales_Model_Order) {
            $hash = Mage::helper('core')->urlEncode("order_id:{$model->getId()}:{$model->getProtectCode()}");
        } elseif ($model instanceof Mage_Sales_Model_Order_Shipment) {
            $hash = Mage::helper('core')->urlEncode("ship_id:{$model->getId()}:{$model->getProtectCode()}");
        } elseif ($model instanceof Mage_Sales_Model_Order_Shipment_Track) {
            $hash = Mage::helper('core')->urlEncode("track_id:{$model->getEntityId()}:{$model->getProtectCode()}");
        }
        if ($hash) {
            $shippingInfoModel = Mage::getModel('shipping/info')->loadByHash($hash);
            $trackingInfo = $shippingInfoModel->getTrackingInfo();
            if (count($trackingInfo) == 1) {
                $trackingModels = array_shift($trackingInfo);
                if (count($trackingModels) == 1) {
                    $tracking = array_shift($trackingModels);
                    if ($tracking instanceof Mage_Shipping_Model_Tracking_Result_Status && $tracking->hasData('url') && $tracking->getUrl() !== '') {
                        return $tracking->getUrl();
                    }
                }
            }
        }
        return false;
    }
}
