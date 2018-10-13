<?php

/**
 * Product:       Xtento_CustomTrackers (1.6.4)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:07:13+00:00
 * Last Modified: 2012-02-26T01:09:58+01:00
 * File:          app/code/local/Xtento/CustomTrackers/Model/System/Config/Source/Shipping/Carriers.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_CustomTrackers_Model_System_Config_Source_Shipping_Carriers
{

    public function toOptionArray()
    {
        $trackingCarriers = array();
        $allCarriers = Mage::getModel('shipping/config')->getAllCarriers(null, true);
        foreach ($allCarriers as $carrierCode => $carrierConfig) {
            if ($carrierConfig->isTrackingAvailable() && !preg_match('/tracker/', $carrierCode)) {
                $trackingCarriers[] = array('value' => $carrierCode, 'label' => Mage::helper('adminhtml')->__($this->_determineCarrierTitle(($carrierCode))));
            }
        }
        return $trackingCarriers;
    }

    public function _determineCarrierTitle($carrierCode)
    {
        if (!isset($this->carriers[$carrierCode])) {
            if ($carrierCode == 'custom') {
                $this->carriers[$carrierCode] = 'Custom';
            } else {
                $this->carriers[$carrierCode] = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
                if (empty($this->carriers[$carrierCode])) {
                    $this->carriers[$carrierCode] = Mage::getStoreConfig('customtrackers/' . $carrierCode . '/title');
                }
            }
        }
        return $this->carriers[$carrierCode];
    }
}
