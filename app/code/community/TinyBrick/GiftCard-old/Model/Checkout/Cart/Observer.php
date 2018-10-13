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
class TinyBrick_GiftCard_Model_Checkout_Cart_Observer
{
	public function saveOrderItemAttributes($observer)
	{
		$orderId 	= $observer->getEvent()->getOrderId();
		$order 		= Mage::getModel('sales/order')->load($orderId);
		$quoteId	= $observer->getEvent()->getQuoteId();
		$quote 		= Mage::getModel('sales/quote')->load($quoteId);
		
		foreach($quote->getAllItems() as $quoteItem) {
			if($quoteItem->getIsGiftcard()) {
				if($quoteItem->getGiftcardNum() && $quoteItem->getIsGcRefill()) {
					//item is a giftcard balance increase
					$card = Mage::getModel('giftcard/giftcard')->getCollection()
						->addFieldToFilter('number', $quoteItem->getGiftcardNum())
						->getFirstItem();
					$card->setBal($card->getBal() + $quoteItem->getPrice());
					$card->setUpdatedAt(now());
					$card->save();
					
					$payment = Mage::getModel('giftcard/payment');
					$payment->setId(null);
					$payment->setGiftcardId($card->getId());
					$payment->setQuoteId($quote->getId());
					$payment->setOrderId($order->getId());
					$payment->setAmount($quoteItem->getPrice());
					$payment->setCreatedAt(now());
					$payment->setIsRefill(1);
					$payment->save();
				} else {
					//new card, generate number/pin
					$card = Mage::getModel('giftcard/giftcard');
					$card->setId(null);
					$card->setNumber(Mage::helper('giftcard')->generateCardNumber());
					$quoteItem->setGiftcardNum($card->getNumber());
					$quoteItem->save();
					$card->setBal($quoteItem->getPrice());
					$card->setOrderId($order->getId());
					$card->setOrderAmount($quoteItem->getPrice());
					$card->setCreatedAt(now());
					$card->setUpdatedAt(now());
					if($quoteItem->getGiftcardEmail()) {
						//item is a printable giftcard
						$sent = Mage::getModel('core/email_template')
							->sendTransactional(
								Mage::getStoreConfig('sales/giftcard/trans_id'),
								array('email' => Mage::getStoreConfig('sales/giftcard/send_email'), 'name' => Mage::getStoreConfig('sales/giftcard/send_email_name')), 
								$quoteItem->getGiftcardEmail(), 
								$quoteItem->getGiftcardEmail(), 
								array('number' => $card->getNumber(), 'amount'=> money_format('$%i', $card->getBal()), 'msg' => $quoteItem->getGiftcardMsg())
						);
						if($sent) {
							$card->setShipped(1);
						} else {
							$card->setShipped(0);
						}
						$card->setType(2);
						$card->setToEmail($quoteItem->getGiftcardEmail());
						$card->setToMsg($quoteItem->getGiftcardMsg());	
					} else {
						$card->setShipped(0);
						$card->setType(1);
					}
					$card->save();
				}
			}
		}
		//set order number on gift card payments
		$giftcards = Mage::getModel('giftcard/payment')->getCollection()
			->addFieldToFilter('quote_id', $quote->getId());
		foreach($giftcards as $giftcard) {
			$giftcard->setOrderId($order->getId());
			$giftcard->save();
		}
	}
}