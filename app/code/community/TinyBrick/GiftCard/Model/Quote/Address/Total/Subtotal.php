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
class TinyBrick_GiftCard_Model_Quote_Address_Total_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Subtotal
{
    protected function _initItem($address, $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $quoteItem = $item->getAddress()->getQuote()->getItemById($item->getQuoteItemId());
        }
        else {
            $quoteItem = $item;
        }
        $product = $quoteItem->getProduct();
        if (!$product->hasCustomerGroupId()) {
            $product->setCustomerGroupId($quoteItem->getQuote()->getCustomerGroupId());
        }

        /**
         * Quote super mode flag meen whot we work with quote without restriction
         */
        if ($item->getQuote()->getIsSuperMode()) {
            if (!$product) {
                return false;
            }
        }
        else {
            if (!$product || !$product->isVisibleInCatalog()) {
                return false;
            }
        }

        if ($quoteItem->getParentItem() && $quoteItem->isChildrenCalculated()) {
            $finalPrice = $quoteItem->getParentItem()->getProduct()->getPriceModel()->getChildFinalPrice(
               $quoteItem->getParentItem()->getProduct(),
               $quoteItem->getParentItem()->getQty(),
               $quoteItem->getProduct(),
               $quoteItem->getQty()
            );
            if(!$item->getIsGiftcard()) {
            	$item->setPrice($finalPrice)
            		->setBaseOriginalPrice($finalPrice);
            }
            $item->calcRowTotal();
        } else if (!$quoteItem->getParentItem()) {
            $finalPrice = $product->getFinalPrice($quoteItem->getQty());
            if(!$item->getIsGiftcard()) {
            	$item->setPrice($finalPrice)
            		->setBaseOriginalPrice($finalPrice);
            }
            $item->calcRowTotal();
			if(version_compare('1.4.0', Mage::getVersion(), '<=')) {
				$this->_addAmount($item->getRowTotal());
            	$this->_addBaseAmount($item->getBaseRowTotal());
			} else {
				$address->setSubtotal($address->getSubtotal() + $item->getRowTotal());
            	$address->setBaseSubtotal($address->getBaseSubtotal() + $item->getBaseRowTotal());
			}
            $address->setTotalQty($address->getTotalQty() + $item->getQty());
        }

        return true;
    }
}
