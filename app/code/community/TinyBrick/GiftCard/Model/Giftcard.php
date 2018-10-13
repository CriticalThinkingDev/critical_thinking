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
class TinyBrick_GiftCard_Model_Giftcard extends Mage_Core_Model_Abstract
{
	public function _construct()
    {    
        parent::_construct();
    	$this->_init('giftcard/giftcard');
    }
    
    protected function _getCard($number)
    {
    	return $this->getCollection()
    		->addFieldToFilter('number', $number)
    		->getFirstItem();
    }
    
    public function auth($payment, $amount)
    {
    	$gcInfo = unserialize($payment->getAdditionalData());
		$cardNum = $gcInfo['gc_number'];

    	$card = $this->_getCard($cardNum);
    	if($amount <= $card->getBal()) {
    		return $this->capturePayment($cardNum, $amount);
    	} else {
    		Mage::throwException(Mage::helper('giftcard')->__('Payment authorization transaction has been declined.'));
    	}
    }
    
    public function capturePayment($cardNum, $amount)
    {
    	$card = $this->_getCard($cardNum);
    	$card->setBal($card->getBal() - $amount);
    	$card->save();
    	return $this;
    }
    
    public function addToBalance($cardNum, $pin, $amount)
    {
    	$card = $this->_getCard($cardNum, $pin);
    	$card->setBal($card->getBal() + $amount);
    	$card->save();
    	return $this;
    }
    
    public function refund($payment, $amount = null)
    {
    	$gcInfo = unserialize($payment->getAdditionalData());
		$cardNum = $gcInfo['gc_number'];
    	$card = $this->_getCard($cardNum);
    	$card->setBal($card->getBal() + $amount);
    	$card->save();
    	return $this;
    }
    
    public function removeAuths()
    {
    	$mdl = Mage::getModel('giftcard/payment')->getCollection();
    	foreach($mdl as $payment) {
    		if($payment->getOrderId() === NULL && $payment->getQuoteId()) {
    			$curtime = time();
    			$paytime = strtotime($payment->getCreatedAt());
    			$pastLimit = $curtime - 3600;
    			if($paytime < $pastLimit) {
    				//add funds back on card
    				$giftcard = Mage::getModel('giftcard/giftcard')->load($payment->getGiftcardId());
    				$giftcard->setBal($giftcard->getBal() + $payment->getAmount());
    				$giftcard->save();
    				
    				$payment->delete();
    			}
    		}
    	}
    }
    
    /**********START :: Added by Krishinc Developer to get Giftcard if applied to order ****/
    public function getGiftCards($quote_id)
    {  
        $cards = '';
        if($quote_id){
            $cards = Mage::getModel('giftcard/payment')->getCollection()
                ->addFieldToFilter('quote_id', $quote_id);
            $cards->getSelect()
                ->join(array('gc' => (string)Mage::getConfig()->getTablePrefix().'giftcard_entity'),
                    'main_table.giftcard_id = gc.giftcard_id',
                    array('number'));
          }
        return $cards;
    }
    /*****END*****/
}