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
class TinyBrick_GiftCard_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
    {
		$this->loadLayout();
		$this->renderLayout();
    }
    
    public function checkBalanceAction()
    {
    	if($bal = Mage::helper('giftcard')->cardBalance($this->getRequest()->getParam('cardnum'))) {
    		$this->getResponse()->setBody(number_format($bal, 2));
    	} else {
    		$this->getResponse()->setBody("<p>Your card number or pin is not valid</p>");
    	}
    }
    
    public function checkCardAction()
    {
    	if($bal = Mage::helper('giftcard')->cardBalance($this->getRequest()->getParam('cardnum'))) {
    		$this->getResponse()->setBody("valid");
        } else {
    		$this->getResponse()->setBody("not valid");
    	}
    }
    
    public function getTotalAction()
    { 
    	$quote = $this->_getQuote();
    	$quote->collectTotals();
    	$quote->save();
    	$this->getResponse()->setBody('Order Total: $' . number_format($quote->getBaseGrandTotal(), 2));
    }
    
    public function applyCardAction()
    {   
    	$data = $this->getRequest()->getParams();
    	$quote = $this->_getQuote();
    	$amount = (float)$data['amount'];
        
        $reloaded = Mage::getSingleton('core/session')->getGiftCardNumber();
        
        Mage::getSingleton('core/session')->unsetGiftCardNumber();

    	if($amount > $quote->getGrandTotal()) {
    		$amount = $quote->getGrandTotal();
    	}
    	if($amount < 0.01) {
    		$this->getResponse()->setBody("invalid amount");
    		return true;
    	}
    	//check for valid card
    	$giftcard = Mage::getModel('giftcard/giftcard')->getCollection()
    		->addFieldToFilter('number', $data['cardNum'])
    		->getFirstItem();
    	if(!$giftcard->getId()) {
    		$this->getResponse()->setBody("Invalid Card");
    		return true;
    	}
    	if($giftcard->getBal() < $amount) {
    		$this->getResponse()->setBody("Card Balance Too Low.");
    		return true;
    	}

        //check to see if this card is the same as the reloaded card
        if($reloaded == $data['cardNum']){
            $reloaded = '';
            $this->getResponse()->setBody("Card Type");
            return true;
            
        }
        
    	//check to see if this card is already applied
    	$cardPay = Mage::getModel('giftcard/payment')->getCollection()
    		->addFieldToFilter('quote_id', $quote->getId())
    		->addFieldToFilter('giftcard_id', $giftcard->getId())
    		->getFirstItem();
    	if($cardPay->getId()) {
    		$this->getResponse()->setBody("This card is already applied to your order");
    		return true;
    	}
    	
    	//apply new card
    	$giftcard->setBal($giftcard->getBal() - $amount);
    	$giftcard->save();
    	$newCard = Mage::getModel('giftcard/payment');
    	$newCard->setId(null);
    	$newCard->setGiftcardId($giftcard->getId());
    	$newCard->setQuoteId($quote->getId());
    	$newCard->setAmount($amount);
    	$newCard->setCreatedAt(now());
    	$newCard->save();
    	$quote->collectTotals();
    	$quote->save();
    	$html = $this->getAjaxHtml();
    	$this->getResponse()->setBody($html);
    }
    
    public function removeCardAction()
    {
    	$cardNum = $this->getRequest()->getParam('cardNum');
    	$quote = $this->_getQuote();
    	$card = Mage::getModel('giftcard/giftcard')->getCollection()
    		->addFieldToFilter('number', $cardNum)
    		->getFirstItem();
    	
    	$cardPay = Mage::getModel('giftcard/payment')->getCollection()
    		->addFieldToFilter('quote_id', $quote->getId())
    		->addFieldToFilter('giftcard_id', $card->getId())
    		->getFirstItem();
    	$card->setBal($card->getBal() + $cardPay->getAmount());
    	$card->save();
    	$cardPay->delete();
    	$quote->collectTotals();
    	$quote->save();
    	$html =$this->getAjaxHtml();
        $this->getResponse()->setBody($html);
    }
    
    public function getAjaxHtml()
    {
    	//build html output for ajax
    	$html = '';
    	$cards = Mage::getModel('giftcard/payment')->getCollection()
    		->addFieldToFilter('quote_id', $this->_getQuote()->getId());
    	$cards->getSelect()
    		->join(array('gc' => (string)Mage::getConfig()->getTablePrefix().'giftcard_entity'),
    		'main_table.giftcard_id = gc.giftcard_id',
    		array('gc.number'));
    		
    	foreach($cards as $card) {
    		$html .= 'Gift Card: $' . number_format($card->getAmount(), 2) . ' (' . $card->getNumber() . ') <a href="javascript:void(0);" onclick="removeCard(\''.$card->getNumber().'\')">remove</a><br />';
    	}
    	$html .= '<br />';
    	$total = 'Order Total: $' . number_format($this->_getQuote()->getBaseGrandTotal(), 2);
    	$arr = array('html'=>$html, 'total'=>$total);
    	return Zend_Json::encode($arr);
    }
    
    
    public function buyCardAction()
	{
		try {
			$vals = $this->getRequest()->getParams();
			if($vals['type'] == 'print') {
				$sku = Mage::getStoreConfig('sales/giftcard/virtual_card_sku');
			} else {
				$sku = Mage::getStoreConfig('sales/giftcard/ship_card_sku');
			}
			 
			$quote = $this->_getQuote();
			$product = $this->_getProduct($sku);
			$this->_assignStock($product);
			$cardItem = $quote->addProduct($product);
			$cardItem->setPrice($vals['amount']);
			$cardItem->setBasePrice($vals['amount']);
			$cardItem->setGiftcardMsg($vals['msg']);
			$cardItem->setQty(1);
			$cardItem->setWeight(0);
			$cardItem->setIsGiftcard(1);
			
			if($vals['email'] && $vals['type'] == 'print') {
				$cardItem->setGiftcardEmail($vals['email']);
			}
			$cardItem->save();
			$quote->collectTotals()->save();

			$this->_getSession()->addSuccess($product->getName() . " was successfully added to your shopping cart.");
			$this->_redirect('checkout/cart');
		} catch(Exception $e) {
			$this->_getSession()->addException($e, $this->__('Cannot add item to shopping cart'));
			$this->_redirect('checkout/cart');
		}
	}
	public function addToCardAction()
	{
		try {
			$vals = $this->getRequest()->getParams();
			$sku = Mage::getStoreConfig('sales/giftcard/add_card_sku');
			
			$quote = $this->_getQuote();
			$product = $this->_getProduct($sku);
			$this->_assignStock($product);
			$cardItem = $quote->addProduct($product);
			$cardItem->setPrice($vals['amount']);
			$cardItem->setQty(1);
			$cardItem->setWeight(0);
			$cardItem->setIsGiftcard(1);
			$cardItem->setGiftcardNum($vals['cardnum']);
			$cardItem->setIsGcRefill(1);
			$cardItem->save();
			$quote->collectTotals()->save();
                        
                        // This adds the card number to the session
                        Mage::getSingleton('core/session')->setGiftCardNumber($vals['cardnum']);

                        
			$this->_getSession()->addSuccess($product->getName() . " was successfully added to your shopping cart.");
			$this->_redirect('checkout/cart');
		} catch(Exception $e) {
			$this->_getSession()->addException($e, $this->__('Cannot add item to shopping cart'));
			$this->_redirect('checkout/cart');
		}
	}
	
	protected function _getQuote()
	{
		if(!Mage::getSingleton('checkout/session')->getQuoteId()) {
			$quote = Mage::getModel('sales/quote')
    			->setId(null)
    			->setStoreId(1)
    			->setCustomerId('NULL')
    			->setCustomerTaxClassId(1);
    		$quote->save();
			Mage::getSingleton('checkout/session')->setQuoteId($quote->getId());
		} else {
			$quote = Mage::getSingleton('checkout/session')->getQuote();
		}
		return $quote;
	}
	
	protected function _getProduct($sku)
	{
		return Mage::getModel('catalog/product')->getCollection()
			->addAttributeToFilter('sku', $sku)
			->addAttributeToSelect('*')
			->getFirstItem();
	}
	
	protected function _assignStock($product)
	{
		$stockItem = Mage::getModel('cataloginventory/stock_item');
		$stockItem->assignProduct($product);
		if(!$stockItem->getUseConfigManageStock()) {
			$stockItem->setData('is_in_stock', 1);
			$stockItem->setData('manage_stock', 0);
			$stockItem->setData('use_config_manage_stock', 0);
			$stockItem->setData('min_sale_qty', 0);
			$stockItem->setData('use_config_min_sale_qty', 0);
			$stockItem->setData('max_sale_qty', 1000);
			$stockItem->setData('use_config_max_sale_qty', 0);
			$stockItem->save();
		}
	}
	
	protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function testApiAction()
    {
		$proxy = new SoapClient('http://127.0.0.1/gift_card/api/soap/?wsdl');
		$sessionId = $proxy->login('api_user', 'api_password');
		$balance = $proxy->call($sessionId, 'giftcard.balance', array('12341234'));
		$this->getResponse()->setBody($balance);
    }
}