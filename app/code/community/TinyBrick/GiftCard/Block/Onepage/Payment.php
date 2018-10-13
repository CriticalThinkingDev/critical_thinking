<?php
class TinyBrick_GiftCard_Block_Onepage_Payment extends Mage_Checkout_Block_Onepage_Payment
{
    protected function _toHtml()
    {
    	$this->setTemplate('checkout/onepage/gc-payment.phtml');
        if (!$this->getTemplate()) {
            return '';
        }
        $html = $this->renderView();
        return $html;
    }
    public function getCards()
    {
    	$cards = Mage::getModel('giftcard/payment')->getCollection()
    		->addFieldToFilter('quote_id', $this->getQuote()->getId());
    	$cards->getSelect()
    		->join(array('gc' => (string)Mage::getConfig()->getTablePrefix().'giftcard_entity'),
    		'main_table.giftcard_id = gc.giftcard_id',
    		array('gc.number'));
    		
		return $cards;
    }
    
}
