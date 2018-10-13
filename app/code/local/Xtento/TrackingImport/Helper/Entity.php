<?php

/**
 * Product:       Xtento_TrackingImport (2.3.1)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:06:49+00:00
 * Last Modified: 2014-06-15T14:00:10+02:00
 * File:          app/code/local/Xtento/TrackingImport/Helper/Entity.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_TrackingImport_Helper_Entity extends Mage_Core_Helper_Abstract
{
    public function getPluralEntityName($entity) {
        return $entity;
    }

    public function getEntityName($entity) {
        $entities = Mage::getModel('xtento_trackingimport/import')->getEntities();
        if (isset($entities[$entity])) {
            return $entities[$entity];
        }
        return "";
    }
}