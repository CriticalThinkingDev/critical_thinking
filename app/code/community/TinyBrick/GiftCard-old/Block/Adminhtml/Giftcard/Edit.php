<?php
/**
 * Open Commerce LLC Commercial Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Commerce LLC Commercial Extension License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.tinybrick.com/license/commercial-extension
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tinybrick.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this package to newer
 * versions in the future. 
 *
 * @category   TinyBrick
 * @package    TinyBrick_GiftCard
 * @copyright  Copyright (c) 2010 TinyBrick Inc. LLC
 * @license    http://www.tinybrick.com/license/commercial-extension
 */
class TinyBrick_GiftCard_Block_Adminhtml_Giftcard_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'giftcard';
        $this->_controller = 'adminhtml_giftcard';
        
        $this->_updateButton('save', 'label', Mage::helper('giftcard')->__('Save Entry'));
        $this->_updateButton('delete', 'label', Mage::helper('giftcard')->__('Delete Entry'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
			Event.observe('type','change',function(){
				if($('type').value == '1'){
					$('send_email').value = '0';
					$('send_email').disable();
				}else if($('type').value == '2'){
					$('send_email').enable();
				}
			})
            function toggleEditor() {
                if (tinyMCE.getInstanceById('recipes_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'recipes_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'recipes_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('giftcard_data') && Mage::registry('giftcard_data')->getId() ) {
            return Mage::helper('giftcard')->__("Gift Card '%s'", $this->htmlEscape(Mage::registry('giftcard_data')->getNumber()));
        } else {
            return Mage::helper('giftcard')->__('Add Entry');
        }
    }
}