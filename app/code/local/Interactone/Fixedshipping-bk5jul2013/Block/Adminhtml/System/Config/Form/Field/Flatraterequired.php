<?php

/**
 * @category   Interactone
 * @package    Interactone_Fixedshipping
 */
class Interactone_Fixedshipping_Block_Adminhtml_System_Config_Form_Field_Flatraterequired
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Get element ID of the dependent field's parent row
     *
     * @param object $element
     * @return String
     */
    protected function _getRowElementId($element)
    {
        return 'row_' . $element->getId();
    }

    /**
     * Override method to output our custom HTML with JavaScript
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if (!Mage::helper('interactone_fixedshipping')->isFlatRateEnabled()) {
            $url = Mage::helper('adminhtml')->getUrl('*/system_config/edit', array('section' => 'interactone_fixedshipping'));
            $element->setDisabled('disabled')
                ->setValue("")
                ->setComment(sprintf('Flat Rate must be enabled for <a href="%s">Fixed Shipping module</a>.', $url));
        }

        return parent::_getElementHtml($element);
    }
}