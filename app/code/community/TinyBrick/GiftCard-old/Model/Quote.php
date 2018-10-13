<?php
class TinyBrick_GiftCard_Model_Quote extends Mage_Sales_Model_Quote
{
    public function collectTotals()
    {
        /**
         * Protect double totals collection
         */
        if ($this->getTotalsCollectedFlag()) {
            return $this;
        }
        Mage::dispatchEvent(
            $this->_eventPrefix . '_collect_totals_before',
            array(
                $this->_eventObject=>$this
            )
        );

        $this->setSubtotal(0);
        $this->setBaseSubtotal(0);

        $this->setSubtotalWithDiscount(0);
        $this->setBaseSubtotalWithDiscount(0);

        $this->setGrandTotal(0);
        $this->setBaseGrandTotal(0);

		//get giftcard totals to deduct from the total
		$cards = Mage::getModel('giftcard/payment')->getCollection()
			->addFieldToFilter('quote_id', $this->getId());
		$gcAmount = 0;
		if($cards) {
			foreach($cards as $card) {
				$gcAmount = $gcAmount + $card->getAmount();
			}
		}
		
        foreach ($this->getAllAddresses() as $address) {
            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);

            $address->collectTotals();
            
            $this->setSubtotal((float) $this->getSubtotal()+$address->getSubtotal());
            $this->setBaseSubtotal((float) $this->getBaseSubtotal()+$address->getBaseSubtotal());

            $this->setSubtotalWithDiscount((float) $this->getSubtotalWithDiscount()+$address->getSubtotalWithDiscount());
            $this->setBaseSubtotalWithDiscount((float) $this->getBaseSubtotalWithDiscount()+$address->getBaseSubtotalWithDiscount());

            $this->setGrandTotal((float) $this->getGrandTotal()+$address->getGrandTotal());
            $this->setBaseGrandTotal((float) $this->getBaseGrandTotal()+$address->getBaseGrandTotal());
        }

        Mage::helper('sales')->checkQuoteAmount($this, $this->getGrandTotal());
        Mage::helper('sales')->checkQuoteAmount($this, $this->getBaseGrandTotal());
        
        //remove gc amount
		if($gcAmount) {
			if($this->getIsVirtual()) {
				$this->getBillingAddress()->setSubtotal($this->getBillingAddress()->getSubtotal() - $gcAmount);
				$this->getBillingAddress()->setBaseSubtotal($this->getBillingAddress()->getBaseSubtotal() - $gcAmount);

				$this->getBillingAddress()->setSubtotalWithDiscount($this->getBillingAddress()->getSubtotalWithDiscount() - $gcAmount);
				$this->getBillingAddress()->setBaseSubtotalWithDiscount($this->getBillingAddress()->getBaseSubtotalWithDiscount() - $gcAmount);
							
				$this->getBillingAddress()->setGrandTotal($this->getBillingAddress()->getGrandTotal() - $gcAmount);
				$this->getBillingAddress()->setBaseGrandTotal($this->getBillingAddress()->getBaseGrandTotal() - $gcAmount);
			} else {
				$this->getShippingAddress()->setSubtotal($this->getShippingAddress()->getSubtotal() - $gcAmount);
				$this->getShippingAddress()->setBaseSubtotal($this->getShippingAddress()->getBaseSubtotal() - $gcAmount);

				$this->getShippingAddress()->setSubtotalWithDiscount($this->getShippingAddress()->getSubtotalWithDiscount() - $gcAmount);
				$this->getShippingAddress()->setBaseSubtotalWithDiscount($this->getShippingAddress()->getBaseSubtotalWithDiscount() - $gcAmount);
				
				$this->getShippingAddress()->setGrandTotal($this->getShippingAddress()->getGrandTotal() - $gcAmount);
				$this->getShippingAddress()->setBaseGrandTotal($this->getShippingAddress()->getBaseGrandTotal() - $gcAmount);
			}
			$this->setSubtotal((float) $this->getSubtotal()-$gcAmount);
            $this->setBaseSubtotal((float) $this->getBaseSubtotal()-$gcAmount);

            $this->setSubtotalWithDiscount((float) $this->getSubtotalWithDiscount()-$gcAmount);
            $this->setBaseSubtotalWithDiscount((float) $this->getBaseSubtotalWithDiscount()-$gcAmount);
            
			$this->setGrandTotal($this->getGrandTotal() - $gcAmount);
			$this->setBaseGrandTotal($this->getBaseGrandTotal() - $gcAmount);
		}
		

        $this->setItemsCount(0);
        $this->setItemsQty(0);
        $this->setVirtualItemsQty(0);

        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if (($children = $item->getChildren()) && $item->isShipSeparately()) {
                foreach ($children as $child) {
                    if ($child->getProduct()->getIsVirtual()) {
                        $this->setVirtualItemsQty($this->getVirtualItemsQty() + $child->getQty()*$item->getQty());
                    }
                }
            }

            if ($item->getProduct()->getIsVirtual()) {
                $this->setVirtualItemsQty($this->getVirtualItemsQty() + $item->getQty());
            }
            $this->setItemsCount($this->getItemsCount()+1);
            $this->setItemsQty((float) $this->getItemsQty()+$item->getQty());
        }

        $this->setData('trigger_recollect', 0);
        $this->_validateCouponCode();
        
        Mage::dispatchEvent(
            $this->_eventPrefix . '_collect_totals_after',
            array(
                $this->_eventObject=>$this
            )
        );

        $this->setTotalsCollectedFlag(true);
        return $this;
    }
}
