<?php

/**
 * Product:       Xtento_CustomTrackers (1.6.4)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:07:13+00:00
 * Last Modified: 2012-12-28T16:21:03+01:00
 * File:          app/code/local/Xtento/CustomTrackers/Model/Observer.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_CustomTrackers_Model_Observer
{
    public function controller_action_predispatch_adminhtml($event)
    {
        // Check if this module was made for the edition (CE/PE/EE) it's being run in
        $controller = $event->getControllerAction();
        if ($controller->getRequest()->getControllerName() == 'system_config' && $controller->getRequest()->getParam('section') == 'customtrackers') {
            if (!Mage::registry('edition_warning_shown')) {
                if (Xtento_CustomTrackers_Helper_Data::EDITION !== 'CE' && Xtento_CustomTrackers_Helper_Data::EDITION !== '') {
                    if (Mage::helper('xtcore/utils')->getIsPEorEE() && Mage::helper('customtrackers')->getModuleEnabled()) {
                        if (Xtento_CustomTrackers_Helper_Data::EDITION !== 'EE') {
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtcore')->__('Attention: The installed Custom Carrier Trackers version is not compatible with the Enterprise Edition of Magento. The compatibility of the currently installed extension version has only been confirmed with the Community Edition of Magento. Please go to <a href="https://www.xtento.com" target="_blank">www.xtento.com</a> to purchase or download the Enterprise Edition of this extension in our store if you\'ve already purchased it.'));
                        }
                    }
                }
                Mage::register('edition_warning_shown', true);
            }
        }
    }
}