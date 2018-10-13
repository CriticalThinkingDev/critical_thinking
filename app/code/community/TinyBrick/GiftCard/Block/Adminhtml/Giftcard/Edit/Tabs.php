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
class TinyBrick_GiftCard_Block_Adminhtml_Giftcard_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('giftcard_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('giftcard')->__('Gift Card'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('giftcard')->__('Details'),
          'title'     => Mage::helper('giftcard')->__('Details'),
          'content'   => $this->getLayout()->createBlock('giftcard/adminhtml_giftcard_edit_tab_form')->toHtml(),
      ));
      
      $this->addTab('reporting_section', array(
          'label'     => Mage::helper('giftcard')->__('History'),
          'title'     => Mage::helper('giftcard')->__('History'),
          'content'   => $this->getLayout()->createBlock('giftcard/adminhtml_giftcard_edit_tab_history')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}