<?php
/**
 * Downloadplus CRON Jobs
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Cron
{
	
	public function processMissingSerialnumbers()
	{
		$helper = Mage::helper('downloadplus');
		
		/* Downloadable Products */
		$links = Mage::getModel('downloadplus/link')->getCollection();
		foreach ($links as $link) {
			$product = Mage::getModel('catalog/product')->load($link->getProductId());
			if ($helper->hasSerialnumbers($product) && !$helper->hasSerialnumbersDeactivated($product)) {
				$extension = $link->getExtension();
				if ($extension->hasSerialnumbers()) {
					$orderItems = Mage::getModel('sales/order_item')->getCollection()
									->addFieldToFilter('product_id', Array('eq'=>$link->getProductId()));
					
					foreach ($orderItems as $orderItem) {
						$order = $orderItem->getOrder();
						$orderItemStatusToEnable = Mage::getStoreConfig(Mage_Downloadable_Model_Link_Purchased_Item::XML_PATH_ORDER_ITEM_STATUS, $order->getStoreId());
						if ($orderItem->getStatusId() == $orderItemStatusToEnable) {
							Mage::dispatchEvent('downloadplus_order_save_after_downloadable_create_serialnumber', Array('order'=>$order, 'order_item'=>$orderItem));
						}
					}
				}
			}
		}

		/* General Products */
		$products = Mage::getModel('catalog/product')->getCollection()
						->addAttributeToFilter('type_id', Array('neq', Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE));
		
		foreach ($products as $product) {
			if ($helper->hasSerialnumbers($product) && !$helper->hasSerialnumbersDeactivated($product)) {
				$orderItems = Mage::getModel('sales/order_item')->getCollection()
								->addFieldToFilter('product_id', Array('eq'=>$product->getId()));
					
				foreach ($orderItems as $orderItem) {
					$order = $orderItem->getOrder();
					$orderItemStatusToEnable = Mage::getStoreConfig(Mage_Downloadable_Model_Link_Purchased_Item::XML_PATH_ORDER_ITEM_STATUS, $order->getStoreId());
					if ($orderItem->getStatusId() == $orderItemStatusToEnable) {
						Mage::dispatchEvent('downloadplus_order_save_after_product_create_serialnumber', Array('order'=>$order, 'order_item'=>$orderItem));
					}
				}
			}
		}
		
	}
	
}