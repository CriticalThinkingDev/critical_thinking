<?php
/**
 * Extending one page checkout processing model
 */
class TinyBrick_GiftCard_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        //find out if the customer is using a giftcard(s)
		$cards = Mage::getModel('giftcard/payment')->getCollection()
			->addFieldToFilter('quote_id', $this->getQuote()->getId());
		if(count($cards)) {
			if($this->getQuote()->getGrandTotal() == 0) {
				$data = array('method'=>'free');
			}
		}
		
        if ($this->getQuote()->isVirtual()) {
            $this->getQuote()->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $this->getQuote()->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        $payment = $this->getQuote()->getPayment();
        $payment->importData($data);

        $this->getQuote()->save();

        $this->getCheckout()
            ->setStepData('payment', 'complete', true)
            ->setStepData('review', 'allow', true);

        return array();
    }
}
