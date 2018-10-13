<?php

/**
 * Product:       Xtento_TrackingImport (2.3.1)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:06:49+00:00
 * Last Modified: 2013-11-03T16:33:42+01:00
 * File:          app/code/local/Xtento/TrackingImport/Model/System/Config/Source/Import/Startup.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_TrackingImport_Model_System_Config_Source_Import_Startup
{

    public function toOptionArray()
    {
        $pages = array();
        foreach (Mage::getBlockSingleton('xtento_trackingimport/adminhtml_widget_menu')->getMenu() as $controllerName => $entryConfig) {
            if (!$entryConfig['is_link']) {
                continue;
            }
            $pages[] = array('value' => $controllerName, 'label' => Mage::helper('xtento_trackingimport')->__($entryConfig['name']));
        }
        return $pages;
    }

}