<?php
/**
 * Downloadplus Event Queue Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.2
 */

class Pisc_Downloadplus_Model_Event_Queue extends Mage_Core_Model_Abstract
{

	const STATUS_PENDING = 'pending';
	const STATUS_PROCESSING = 'processing';
	const STATUS_COMPLETE = 'complete';
	const STATUS_FAILED = 'failed';
	
	protected function _construct()
	{
		parent::_construct();
		$this->_init('downloadplus/event_queue');
	}

	public function setAttributes($value)
	{
		$this->setData('attributes', serialize($value));
		return $this;
	}
	
	public function getAttributes()
	{
		if ($this->getData('attributes')) {
			return unserialize($this->getData('attributes'));
		}
		return null;
	}
	
	public function save()
	{
		if (!$this->getStatus()) {
			$this->setStatus(self::STATUS_PENDING);
		}
		if (!$this->getCreatedAt()) {
			$this->setCreatedAt(Mage::getModel('core/date')->date());
		}
		
		return parent::save();
	}
	
	public function process()
	{
		$result = null;

		if ($this->getStatus()==self::STATUS_PROCESSING) {
			return $result;
		}

		if (!$this->getRelatedId()) {
			$this->setStatus(self::STATUS_COMPLETE);
			$this->save();
			return false;
		}
		
		switch ($this->getEvent()) {
			case 'downloadable-link-update':
				// Process tasks when having a updated Downloadable Link File
				$collection = Mage::getModel('downloadable/link_purchased_item')->getCollection();
				$collection->addFieldToFilter('link_id', array('eq'=>$this->getRelatedId()));
				
				foreach ($collection as $link) {
					if ($orderItem = Mage::getModel('sales/order_item')->load($link->getOrderItemId())) {
						if ($order = Mage::getModel('sales/order')->load($orderItem->getOrderId())) {
							// Trigger Email Delivery Event
							Mage::dispatchEvent('downloadplus_email_delivery', Array('order' => $order, 'order_item' => $orderItem, 'update'=>true));
							$result = true;
						}						
					}
				}
				
				$this->setStatus(self::STATUS_COMPLETE);
				$this->save();
				break;

			case 'downloadable-link-replace':
				// Replaces all purchased Items for a Link with a new Downloadable Link
				if ($attributes = $this->getAttributes()) {
					if (isset($attributes['link_id'])) {
						$ids = $attributes['link_id'];
						if (!is_array($ids)) {
							$ids = explode(',',$attributes['link_id']);
						}
						foreach ($ids as $id) {
							$collection = Mage::getModel('downloadplus/link_purchased_item')->getCollection();
							$collection->addFieldToFilter('link_id', Array('eq'=>$id));
							foreach ($collection as $purchasedLink) {
								// Only non-expired Links are being replaced by setting them to 'EXPIRED' and adding a new Purchased Item Link
								if ($purchasedLink->getStatus()!=Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
									
									// Only create replacement once
									if (Mage::getModel('downloadplus/link_purchased_item')
											->getCollection()
											->addFieldToFilter('order_item_id', Array('eq'=>$purchasedLink->getOrderItemId()))
											->addFieldToFilter('link_id', Array('eq'=>$this->getRelatedId()))
											->getSize()==0)
									{							
									
										$newLink = Mage::getModel('downloadplus/link_purchased_item');
										
										Mage::helper('core')->copyFieldset(
												'downloadplus_purchased_link_item_replace',
												'to_new',
												$purchasedLink,
												$newLink
											);
											
										$newLink->setLinkId($this->getRelatedId());
										
										$link = Mage::getModel('downloadplus/link')->load($newLink->getLinkId());
										if ($link->getId()==$newLink->getLinkId()) {
											Mage::helper('core')->copyFieldset(
												'downloadplus_purchased_link_item_replace',
												'from_link',
												$link,
												$newLink
											);

											$link->setStoreId($purchasedLink->getOrder()->getStoreId());
											if (!$link->getLinkTitle()) {
												$link->setStoreId(0);
											}
											$newLink->setLinkTitle($link->getLinkTitle());
											
											$linkHash = strtr(base64_encode(microtime() . $newLink->getPurchasedId() . $newLink->getOrderItemId() . $newLink->getProductId()), '+/=', '-_,');
											$newLink->setLinkHash($linkHash)
													->setStatus($purchasedLink->getStatus())
													->setCreatedAt(Mage::getModel('core/date')->date())
													->setUpdatedAt(Mage::getModel('core/date')->date())
													->save();

											// Takeover expiration settings of original purchased link
											$extension = $purchasedLink->getExtension();
											$newLinkExtension = $newLink->getExtension();
											$newLinkExtension->setExpiresOn($extension->getExpiresOn())
															->save();
												
											if ($newLink->getId()) {
												$purchasedLink->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED);
												$purchasedLink->save();
												$result = true;
											}
										}
									
									}
								}								
							}
						}
						
					}
				}
				
				$this->setStatus(self::STATUS_COMPLETE);
				$this->save();
				break;

			default:
			    $event = new Varien_Object();
			    $event->setData('queue', $this);
			    $event->setData('result', $result);
			    Mage::dispatchEvent('downloadplus_event_queue_process', $event);
			    
			    $result = $event->getData('result');
			    break;
				
		}
		
		return $result;
	}
	
}