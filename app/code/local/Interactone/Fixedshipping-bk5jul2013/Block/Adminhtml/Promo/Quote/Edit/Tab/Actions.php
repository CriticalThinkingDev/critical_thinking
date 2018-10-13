<?php

class Interactone_Fixedshipping_Block_Adminhtml_Promo_Quote_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Actions
{
    protected function _prepareForm()
    {
    	 
        if (!Mage::helper('interactone_fixedshipping')->isEnabled()) {
            return parent::_prepareForm();
        }

        parent::_prepareForm();

        $form     = $this->getForm();
        $fieldset = $form->getElement('action_fieldset');
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $model    = Mage::registry('current_promo_quote_rule');

        $fieldset->removeField('simple_free_shipping');
        $freeShipping = $fieldset->addField('simple_free_shipping', 'select', array(
            'label'     => Mage::helper('interactone_fixedshipping')->__('Fixed Shipping'),
            'title'     => Mage::helper('interactone_fixedshipping')->__('Fixed Shipping'),
            'name'      => 'simple_free_shipping',
            'options'   => array(
                0 => Mage::helper('salesrule')->__('No'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM    => Mage::helper('salesrule')->__('For matching items only'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS => Mage::helper('salesrule')->__('For shipment with matching items'),
            ),
            'onclick' => 'toggleFixedShipping()',
        ), 'apply_to_shipping');
        $freeShipping->setAfterElementHtml('
            <script>
            function toggleFixedShipping() {
                if ($("rule_simple_free_shipping").value == 0) {
                    $("rule_fixed_shipping_amount").disabled = true;
                } else {
                    $("rule_fixed_shipping_amount").disabled = false;
                }
            }
            </script>
        ');

        $fixedShippingAmount = $fieldset->addField('fixed_shipping_amount', 'text', array(
            'label' => Mage::helper('interactone_fixedshipping')->__('Fixed Shipping Amount'),
            'title' => Mage::helper('interactone_fixedshipping')->__('Fixed Shipping Amount'),
            'name'  => 'fixed_shipping_amount',
            'note'  => "Enter '0.00' for Free Shipping.",
        ), 'simple_free_shipping');
        $model->setFixedShippingAmount($model->getFixedShippingAmount() * 1);

        if (!$model->getSimpleFreeShipping()) {
            $fixedShippingAmount->setDisabled('disabled');
        }

        // Reset form values
        $form->setValues($model->getData());

        return $this;
    }
}