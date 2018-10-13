<?php

class Krishinc_Advancecustomer_Block_Adminhtml_Customer_Edit_Renderer_Newpass extends Mage_Adminhtml_Block_Customer_Edit_Renderer_Newpass
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr>';
        $html.= '<td class="label">'.$element->getLabelHtml().'</td>';
        $html.= '<td class="value">'.$element->getElementHtml().'</td>';
        $html.= '</tr>'."\n";
        $html.= '<tr>';
        $html.= '<td class="label"><label>&nbsp;</label></td>';
        $html.= '<td class="value">'.Mage::helper('customer')->__('or').'</td>';
        $html.= '</tr>'."\n";
        $html.= '<tr>';
        $html.= '<td class="label"><label>&nbsp;</label></td>';
        $html.= '<td class="value"><input type="checkbox" id="account-send-pass" name="'.$element->getName().'" value="auto" onclick="setElementDisable(\''.$element->getHtmlId().'\', this.checked)"/>&nbsp;';
        $html.= '<label for="account-send-pass">'.Mage::helper('customer')->__('Send auto-generated password').'</label></td>';
        $html.= '</tr>'."\n";
        
        if(!$this->getRequest()->getParam('id'))
        {
            $html.='<script type="text/javascript">'
                    . " $('account-send-pass').checked = true; 
                        $('_accountpassword').disabled = true; 
                          ". '</script>';
        }
        return $html;
    }

}
