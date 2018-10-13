<?php

class Interactone_Common_Block_Adminhtml_System_Config_About
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_module = 'interactone_common';
    protected $_name   = 'Interactone Common Module';

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $skinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/interactone/';
        $version = Mage::helper($this->_module)->getModuleVersion();

        $html = '
            <div style="background-color: #fafafa; border: 1px solid #D6D6D6; margin-bottom: 10px; padding: 10px 5px 15px 30px; color: #666">
                <img src="' . $skinUrl . 'images/interactone-logo.png" /><br />
                ' . $this->_name . ' v' . $version . ' by <a href="http://iostore.gostorego.com" target="_blank">InteractOne, Inc.</a><br />
                <strong style="color: #000">
                    Copyright &copy; ' . date('Y') . ' <a href="http://www.interactone.com" target="_blank">InteractOne, Inc.</a>
                </strong>
            </div>
        ';

        return $html;
    }
}