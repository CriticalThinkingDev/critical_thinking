<?php
/**
 * Downloadplus Link History Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusBonus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.1
 */

class Pisc_Downloadplus_Model_Link_History extends Mage_Core_Model_Abstract
{

	protected $_eventPrefix = 'downloadplus_link_history';
	
	protected $_linkPurchasedItem = null;
	
	protected function _construct()
	{
		$this->_init('downloadplus/link_history', 'item_id');
	}
	
	public function setLinkPurchasedItem($link)
	{
		$this->_linkPurchasedItem = $link;
		return $this;
	}
	
	public function getCurrentResource()
	{
		$link = null;
		$recent = null;

		$resource = Mage::helper('downloadplus/resource')->getResource($this->_linkPurchasedItem);
		
		if ($this->_linkPurchasedItem instanceof Mage_Downloadable_Model_Link_Purchased_Item) {
			$link = Mage::getModel('downloadable/link')->load($this->_linkPurchasedItem->getLinkId());
		}
		if ($this->_linkPurchasedItem instanceof Pisc_DownloadplusBonus_Model_Link_Purchased_Bonus_Item) {
			$link = Mage::getModel('downloadplusbonus/link_bonus_item')->load($this->_linkPurchasedItem->getLinkId());
		}
		if ($link && $link->getId()==$this->_linkPurchasedItem->getLinkId()) {
			$recent = $this->getRecent($link);
			$resource = Mage::helper('downloadplus/resource')->getResource($recent);
		} 
		
		return $resource;		
	}
	
	public function getCurrentResourceType()
	{
		$link = null;
		$recent = null;

		$type = Mage::helper('downloadplus/resource')->getResourceType($this->_linkPurchasedItem);
		
		if ($this->_linkPurchasedItem instanceof Mage_Downloadable_Model_Link_Purchased_Item) {
			$link = Mage::getModel('downloadable/link')->load($this->_linkPurchasedItem->getLinkId());
		}
		if ($this->_linkPurchasedItem instanceof Pisc_DownloadplusBonus_Model_Link_Purchased_Bonus_Item) {
			$link = Mage::getModel('downloadplusbonus/link_bonus_item')->load($this->_linkPurchasedItem->getLinkId());
		}
		if ($link && $link->getId()==$this->_linkPurchasedItem->getLinkId()) {
			$recent = $this->getRecent($link);
			$type = Mage::helper('downloadplus/resource')->getResourceType($recent);
		}
		
		return $type;
	}

	public function getRecent($link)
	{
		$resource = null;
		
		if ($link->getId()) {
			$collection = $this->getCollection();
			if ($link instanceof Mage_Downloadable_Model_Link) {
				$collection->addFieldToFilter('link_id', Array('eq'=>$link->getId()));
			}
			if ($link instanceof Pisc_DownloadplusBonus_Model_Link_Bonus_Item) {
				$collection->addFieldToFilter('bonus_link_id', Array('eq'=>$link->getId()));
			}
			$collection->setOrder('updated_at', 'DESC');
			if ($collection->getSize()>0) {
				$recent = $collection->getFirstItem();
				
				if ($link instanceof Mage_Downloadable_Model_Link) {
					$resource = Mage::getModel('downloadable/link')->load($recent->getLinkId());
					if ($resource->getId()!=$recent->getLinkId()) {
					    $resource = false;
					}
				}
				if ($link instanceof Pisc_DownloadplusBonus_Model_Link_Bonus_Item) {
					$resource = Mage::getModel('downloadplusbonus/link_bonus_item')->load($recent->getBonusLinkId());
					if ($resource->getId()!=$recent->getBonusLinkId()) {
					    $resource = false;
					}
				}
			}
		}
		
		if ($resource) {
			return $resource;
		}
		return $link;		
	}
	
	public function addUpdate($link)
	{
		if ($link->getId()) {
		    
		    $this->setProductId($link->getProductId());
			$this->setLinkType($link->getLinkType());
			$this->setLinkUrl($link->getLinkUrl());
			$this->setLinkFile($link->getLinkFile());
	
			if ($link instanceof Mage_Downloadable_Model_Link) {
				$this->setLinkId($link->getId());
			}
			
			if ($link instanceof Pisc_DownloadplusBonus_Model_Link_Bonus_Item) {
				$this->setBonusLinkId($link->getId());
			}
		}
		
		return $this;
	}

	public function searchForId()
	{
		$id = null;
		$collection = $this->getCollection();
		$collection->addFieldToFilter('link_type', Array('eq'=>$this->getData('link_type')));
		$collection->addFieldToFilter('link_url', Array('eq'=>$this->getData('link_url')));
		$collection->addFieldToFilter('link_file', Array('eq'=>$this->getData('link_file')));
		if ($this->getProductId()) {
		    $collection->addFieldToFilter('product_id', Array('eq'=>$this->getData('product_id')));
		} else {
		    $collection->addFieldToFilter('product_id', Array('null' => true));
		}
		if ($this->getLinkId()) {
			$collection->addFieldToFilter('link_id', Array('eq'=>$this->getData('link_id')));
		}
		if ($this->getBonusLinkId()) {
			$collection->addFieldToFilter('bonus_link_id', Array('eq'=>$this->getData('bonus_link_id')));
		}
		
		if ($collection->getSize()>0) {
			$id = $collection->getFirstItem()->getId();
		}
		return $id;
	}
	
	public function save()
	{
		$id = $this->searchForId();
		if (is_null($id) && !$this->getId()) {
			$this->setUpdatedAt(Mage::getModel('core/date')->date());
			return parent::save();
			
			// Add Link Titles to History
			if ($this->getLinkId()) {
    			$collection = Mage::getModel('downloadplus/link_title')->getCollection();
    			$collection->addFieldToFilter('link_id', Array('eq'=>$this->getLinkId()));
    			
    			foreach ($collection as $title) {
    			    Mage::getModel('downloadplus/link_title_history')
                			    ->setLinkId($title->getLinkId())
                			    ->setStoreId($title->getStoreId())
                			    ->setTitle($title->getTitle())
                			    ->setUpdatedAt(Mage::getModel('core/date')->date())
                			    ->save();
    			}
			}				
		}
		$this->setId($id);
		return $this;
	}
	
}