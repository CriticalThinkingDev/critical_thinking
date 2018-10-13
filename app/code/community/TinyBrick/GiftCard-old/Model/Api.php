<?php
class TinyBrick_GiftCard_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	protected $_model = null;
	
	public function __construct()
	{
		$this->_model = Mage::getModel('giftcard/giftcard');
	}
	
	public function balance($cardNum)
	{
		try {
			$giftCard = $this->_getGiftcard($cardNum);
			if(!count($giftCard)) {
				$this->_fault('not_exists', 'Invalid Card');
			}
		} catch(Exception $e) {
			$this->_fault('not_exists', 'Invalid Card');
		}
		return $giftCard->getBal();
	}
	
	public function charge($cardNum, $amount)
	{
		try {
			$giftCard = $this->_getGiftcard($cardNum);
			if(!count($giftCard)) {
				$this->_fault('not_exists', 'Invalid Card');
			}
			$giftCard->setBal($giftCard->getBal() - $amount);
			$giftCard->setUpdatedAt(now());
			$giftCard->save();
			$payment = Mage::getModel('giftcard/payment');
			$payment->setId(null)
				->setGiftcardId($giftCard->getId())
				->setQuoteId(0)
				->setOrderId(0)
				->setAmount($amount)
				->setCreatedAt(now())
				->save();
		} catch(Exception $e) {
			$this->_fault('not_exists', 'Invalid Card');
		}
		return $giftCard->getData();
	}
	
	public function validate($cardNum)
	{
		$card = $this->_getGiftcard($cardNum);
		if($card->getId()) {
			return 'Card Valid';
		} else {
			return 'Card Not Valid';
		}
	}
	
	public function add($amount)
	{
		try {
			$card = Mage::getModel('giftcard/giftcard');
			$card->setId(null);
			$card->setBal($amount);
			$card->setNumber(Mage::helper('giftcard')->generateCardNumber());
			$card->setCreatedAt(now());
			$card->setUpdatedAt(now());
			$card->save();
			return $card->getNumber();
		} catch(Exception $e) {
			$this->_fault('create_error', 'Error creating new card');
		}
	}
	
	public function reload($cardNum, $amount)
	{
		try {
			$giftCard = $this->_getGiftcard($cardNum);
			if(!count($giftCard)) {
				$this->_fault('not_exists', 'Invalid Card');
			}
			$giftCard->setBal($giftCard->getBal() + $amount);
			$giftCard->setUpdatedAt(now());
			$giftCard->save();
		} catch(Exception $e) {
			$this->_fault('not_exists', 'Invalid Card');
		}
		return $giftCard->getData();
	}
	
	protected function _getGiftcard($number)
	{
		return Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('number', $number)
			->getFirstItem();
	}
}