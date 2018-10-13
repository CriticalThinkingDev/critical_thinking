<?php
/**
 * Downloadplus Event Observer Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.2
 */

class Pisc_Downloadplus_Model_Event_Observer
{

	public function eventAddToQueue($observer)
	{
		$event = $observer->getEvent()->getCode();
		$id = $observer->getEvent()->getRelatedId();
		$attributes = $observer->getEvent()->getAttributes();
		
		if ($event && $id) {
			$item = Mage::getModel('downloadplus/event_queue');
			$item->setEvent($event);
			$item->setRelatedId($id);
			$item->setAttributes($attributes);
			$item->save();
		}
	}

	public function cronProcessQueue($observer)
	{
		// Remove complete queued events older than 1 Month
		$dateLimit = Mage::getModel('core/date')->date(null, strtotime(Mage::getModel('core/date')->date().' -1 month'));
		$collection = Mage::getModel('downloadplus/event_queue')->getCollection();
		$collection->addFieldToFilter('status', array('eq'=>Pisc_Downloadplus_Model_Event_Queue::STATUS_COMPLETE));
		$collection->addFieldToFilter('created_at', array('lteq'=>$dateLimit));

		foreach ($collection as $item) {
			$item->delete();
		}
		
		// Process pending queued events		
		$collection = Mage::getModel('downloadplus/event_queue')->getCollection();
		$collection->addFieldToFilter('status', array('eq'=>Pisc_Downloadplus_Model_Event_Queue::STATUS_PENDING))
					->setOrder('created_at', 'ASC');
		
		foreach ($collection as $item) {
			$item->process();
		}
	}

	
	public function eventDownloadableLinkUpdate($observer)
	{
		Mage::helper('downloadplus/download');
		
		$link = $observer->getEvent()->getLink();
		if (isset($link['file'])) {
			$file = Zend_Json_Decoder::decode($link['file']);
			if (isset($file[0])) { $file = $file[0]; } else { $file = Array(); }

			if (isset($link['link_id']) && $link['link_id']!=0) {
				$origLink = Mage::getModel('downloadplus/link')->load($link['link_id']);
				if ((isset($file['status']) && $file['status']=='new') ||
					($origLink->getLinkType()!=$link['type']) || 
					(!empty($link['link_url']) && ($origLink->getLinkUrl()!=$link['link_url'])) ||
					(!empty($link['link_file']) && ($origLink->getLinkFile()!=$link['link_file'])))
				{
					Mage::dispatchEvent('downloadplus_event_queue_add', Array('code' => 'downloadable-link-update', 'related_id' => $link['link_id']));
				}
			}
		}
	}	

	public function eventLinkHistoryAdd($observer)
	{
		$link = $observer->getEvent()->getLink();
		Mage::getModel('downloadplus/link_history')
			->addUpdate($link)
			->save();
	}

}
