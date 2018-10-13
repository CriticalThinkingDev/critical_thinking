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
class TinyBrick_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('giftcard_form', array('legend'=>Mage::helper('giftcard')->__('Gift Card Details')));
     
      $arr = array();
      $collection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('group_id', 1);
      foreach($collection as $view) {
      	$arr[] = array('value'=> $view->getStoreId(), 'label'=> $view->getName());
      }
	  
	  $giftcards = Mage::getModel('giftcard/giftcard')->getCollection()
	  		->addFieldToFilter('giftcard_id', $this->getRequest()->getParam('id'));
	  
	  $giftcards->getSelect()
	  		->joinLeft(array('o' => (string)Mage::getConfig()->getTablePrefix().'sales_flat_order'),
	  			'main_table.order_id = o.entity_id',
	  			array('increment_id'));
	  foreach($giftcards as $giftcard) {
	  }

	  if(isset($giftcard)) {
		  $fieldset->addField('number', 'text', array(
	      		'label'     => Mage::helper('giftcard')->__('GC#'),
	      		'text'		=> $giftcard->getNumber(),
                        'name'      => 'gcnumber'
	      ));
      }
      
      $fieldset->addField('bal', 'text', array(
          'label'     => Mage::helper('giftcard')->__('Balance'),
          'title'     => Mage::helper('giftcard')->__('Balance'),
          'name'      => 'bal',
      ));
      
      if(isset($giftcard)) {
      	  if($giftcard->getOrderId() == 0) {
      	  	 $text = 'Admin Created';
      	  } else {
      	  	 $text = '<a href="'.Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view/",array("order_id" => $giftcard->getOrderId(), "key" => Mage::getSingleton("adminhtml/url")->getSecretKey("sales_order","view"))).'">'.$giftcard->getIncrementId().'</a>';
      	  }
		  $fieldset->addField('increment_id', 'note', array(
	      		'label'     => Mage::helper('giftcard')->__('Ordered From'),
	      		'text'		=> $text
	      ));
	  
	      switch($giftcard->getType()) {
	      	case 1:
	      		$type = 'Physical';
	      		break;
	      	case 2:
	      		$type = 'Virtual';
	      		break;
	      }
	      if(isset($type)) {
		      $fieldset->addField('type', 'note', array(
		      		'label'     => Mage::helper('giftcard')->__('Type'),
		      		'text'		=> $type
		      ));
		  }
		  $fieldset->addField('shipped', 'select', array(
	          'label'     => Mage::helper('giftcard')->__('Printed/Sent'),
	          'name'      => 'shipped',
	          'values'    => array(
	              array(
	                  'value'     => 1,
	                  'label'     => 'Yes',
	              ),
	              array(
	                  'value'     => 0,
	                  'label'     => 'No',
	              ),
	          ),
	      ));
      } else {
      	  $fieldset->addField('type', 'select', array(
	          'label'     => Mage::helper('giftcard')->__('Type'),
	          'name'      => 'type',
	          'values'    => array(
	              array(
	                  'value'     => 1,
	                  'label'     => 'Physical',
	              ),
	
	              array(
	                  'value'     => 2,
	                  'label'     => 'Virtual',
	              ),
	          ),
	      ));
      }

      
			  $fieldset->addField('send_email', 'select', array(
			  'label'     => Mage::helper('giftcard')->__('Email Card to Customer'),
			  'name'      => 'send_email',
			  'disabled'  => 'disabled',	
			  'values'    => array(
			  	  array(
			          'value'     => 0,
			          'label'     => 'No',
			      ),
			      array(
			          'value'     => 1,
			          'label'     => 'Yes',
			      ),

			  ),
			));
	if(isset($type)) {
	      if($type == 'Virtual') {
	      	$fieldset->addField('to_email', 'note', array(
	      		'label'     => Mage::helper('giftcard')->__('Email To'),
	      		'text'		=> $giftcard->getToEmail()
	      	));
	      	$fieldset->addField('to_msg', 'note', array(
	      		'label'     => Mage::helper('giftcard')->__('Email Msg'),
	      		'text'		=> $giftcard->getToMsg()
	      	));
	      }
	  } else {
	  	  $fieldset->addField('to_email', 'text', array(
	          'label'     => Mage::helper('giftcard')->__('Email Address'),
	          'title'     => Mage::helper('giftcard')->__('Email Address'),
	          'name'      => 'to_email',
	      ));
	  	  $fieldset->addField('to_msg', 'text', array(
	          'label'     => Mage::helper('giftcard')->__('Gift Message'),
	          'title'     => Mage::helper('giftcard')->__('Gift Message'),
	          'name'      => 'to_msg',
	      ));
	  }
      
      if ( Mage::getSingleton('adminhtml/session')->getRecipesData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getRecipesData());
          Mage::getSingleton('adminhtml/session')->setRecipesData(null);
      } elseif ( Mage::registry('giftcard_data') ) {
          $form->setValues(Mage::registry('giftcard_data')->getData());
      }
      return parent::_prepareForm();
  }
}