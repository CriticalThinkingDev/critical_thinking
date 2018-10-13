<?php
class Krishinc_Autosendtrack_Block_Buttonseries extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('autosendtrack/adminhtml_index/updateseries'); //

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Update Series Priority')
            ->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }
}
?>