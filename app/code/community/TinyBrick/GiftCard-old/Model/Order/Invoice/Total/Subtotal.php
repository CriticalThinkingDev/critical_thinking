<?php
//class TinyBrick_GiftCard_Model_Order_Invoice_Total_Subtotal extends Mage_Sales_Model_Order_Invoice_Total_Subtotal
//{
    /**
     * Collect invoice subtotal
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Sales_Model_Order_Invoice_Total_Subtotal
     */
    /*
public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $subtotal       = 0;
        $baseSubtotal   = 0;
        $subtotalInclTax= 0;
        $baseSubtotalInclTax = 0;

        $order = $invoice->getOrder();

        foreach ($invoice->getAllItems() as $item) {
            $item->calcRowTotal();

            if ($item->getOrderItem()->isDummy()) {
                continue;
            }

            $subtotal       += $item->getRowTotal();
            $baseSubtotal   += $item->getBaseRowTotal();
            $subtotalInclTax+= $item->getRowTotalInclTax();
            $baseSubtotalInclTax += $item->getBaseRowTotalInclTax();
        }

        $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced();
        $baseAllowedSubtotal = $order->getBaseSubtotal() -$order->getBaseSubtotalInvoiced();
        $allowedSubtotalInclTax = $allowedSubtotal + $order->getHiddenTaxAmount()
                + $order->getTaxAmount() - $order->getTaxInvoiced();
        $baseAllowedSubtotalInclTax = $baseAllowedSubtotal + $order->getBaseHiddenTaxAmount()
                + $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced();

        if ($invoice->isLast()) {
            $subtotal = $allowedSubtotal;
            $baseSubtotal = $baseAllowedSubtotal;
            $subtotalInclTax = $allowedSubtotalInclTax;
            $baseSubtotalInclTax  = $baseAllowedSubtotalInclTax;
        } else {
            $subtotal = min($allowedSubtotal, $subtotal);
            $baseSubtotal = min($baseAllowedSubtotal, $baseSubtotal);
            $subtotalInclTax = min($allowedSubtotalInclTax, $subtotalInclTax);
            $baseSubtotalInclTax = min($baseAllowedSubtotalInclTax, $baseSubtotalInclTax);
        }


		//get giftcard totals to deduct from the invoicec total
		$cards = Mage::getModel('giftcard/payment')->getCollection()
			->addFieldToFilter('order_id', $order->getId());
		$gcAmount = 0;
		if($cards) {
			foreach($cards as $card) {
				$gcAmount = $gcAmount + $card->getAmount();
			}
		}
		
        $invoice->setSubtotal($subtotal - $gcAmount);
        $invoice->setBaseSubtotal($baseSubtotal - $gcAmount);
        $invoice->setSubtotalInclTax($subtotalInclTax - $gcAmount);
        $invoice->setBaseSubtotalInclTax($baseSubtotalInclTax - $gcAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $subtotal - $gcAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseSubtotal - $gcAmount);
        return $this;
*/
    //}
//}
