<?php

/**
 * Product:       Xtento_TrackingImport (2.3.1)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:06:49+00:00
 * Last Modified: 2016-02-03T15:28:52+01:00
 * File:          app/code/local/Xtento/TrackingImport/Model/Import/Action/Abstract.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_TrackingImport_Model_Import_Action_Abstract extends Mage_Core_Model_Abstract
{
    protected $_debugMessages = array();
    protected $_hasUpdatedObject = false;

    protected function getActionSettingByField($fieldName, $fieldToRetrieve)
    {
        if ($fieldToRetrieve == 'enabled' || $fieldToRetrieve == 'value') {
            $fieldToRetrieve = 'default_value';
        }
        $actions = $this->getActions();
        foreach ($actions as $actionId => $actionData) {
            if ($actionData['field'] == $fieldName) {
                if (isset($actionData[$fieldToRetrieve])) {
                    #var_dump($actionData[$fieldToRetrieve]); die();
                    if ($fieldToRetrieve == 'default_value') {
                        $manipulatedFieldValue = Mage::getSingleton('xtento_trackingimport/processor_mapping_action_configuration')->setValueBasedOnFieldData(Mage::registry('xtento_trackingimport_updatedata'), $actionData['config']);
                        if ($manipulatedFieldValue !== -99) {
                            $actionData['default_value'] = $manipulatedFieldValue;
                        }
                    }
                    return $actionData[$fieldToRetrieve];
                } else {
                    return "";
                }
            }
        }
        return false;
    }

    protected function getActionSettingByFieldBoolean($fieldName, $fieldToRetrieve)
    {
        return (bool)$this->getActionSettingByField($fieldName, $fieldToRetrieve);
    }

    protected function addDebugMessage($message)
    {
        array_push($this->_debugMessages, $message);
        return $this;
    }

    public function getDebugMessages()
    {
        return (array)$this->_debugMessages;
    }

    protected function setHasUpdatedObject($bool)
    {
        $this->_hasUpdatedObject = $bool;
        return $this;
    }

    public function getHasUpdatedObject()
    {
        return (bool)$this->_hasUpdatedObject;
    }

    protected function getProfile()
    {
        return Mage::registry('tracking_import_profile');
    }

    protected function getProfileConfiguration()
    {
        return new Varien_Object($this->getProfile()->getConfiguration());
    }
}