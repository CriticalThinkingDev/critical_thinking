<?php

/**
 * Product:       Xtento_TrackingImport (2.3.1)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:06:49+00:00
 * Last Modified: 2013-11-03T16:33:42+01:00
 * File:          app/code/local/Xtento/TrackingImport/Block/Adminhtml/Source/Edit/Tabs.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_TrackingImport_Block_Adminhtml_Source_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('source_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xtento_trackingimport')->__('Import Source'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label' => Mage::helper('xtento_trackingimport')->__('Source Configuration'),
            'title' => Mage::helper('xtento_trackingimport')->__('Source Configuration'),
            'content' => $this->getLayout()->createBlock('xtento_trackingimport/adminhtml_source_edit_tab_configuration')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}