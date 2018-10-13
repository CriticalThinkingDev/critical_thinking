<?php
class Krishinc_Overridepo_Model_Order_Api extends Mage_Sales_Model_Order_Api 
{
	/**
     * Retrieve full order information
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function info($orderIncrementId)
    {
     
        $result = parent::info($orderIncrementId);
        $order = $this->_initOrder($orderIncrementId);
         
        if(isset($result['billing_address']['street']) && $result['billing_address']['street'] != '') {
            $streetLines = $order->getBillingAddress()->getStreet();
            unset($result['billing_address']['street']);
            foreach ($streetLines as $i=>$line) {
                $result['billing_address']['address_street'.($i+1)] = $line; 
            } 
            if(!isset($result['billing_address']['address_street2']))
            {
                $result['billing_address']['address_street2'] = '';
            } 
        }  
 
        if(isset($result['shipping_address']['street']) && $result['shipping_address']['street'] != '') {
            $streetLines1 = $order->getShippingAddress()->getStreet();
            unset($result['shipping_address']['street']);
            foreach ($streetLines1 as $i=>$line) {
                $result['shipping_address']['address_street'.($i+1)] = $line; 
            }
            if(!isset($result['shipping_address']['address_street2']))
            {
                $result['shipping_address']['address_street2'] = '';
            } 
         } 
        if(isset($result['shipping_address']['customer_type']) && $result['shipping_address']['customer_type'] == 'O'){$result['shipping_address']['customer_type'] = 'P';}
        if(isset($result['billing_address']['customer_type']) && $result['billing_address']['customer_type'] == 'O'){$result['billing_address']['customer_type'] = 'P';} 
 
        return $result;
    }

}