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
class TinyBrick_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_History extends Mage_Adminhtml_Block_Abstract
{
	public function _construct() 
	{
		$this->setTemplate('giftcard/history.phtml');
	}
	public function getPurchase($id)
	{
		$mdl = Mage::getModel('giftcard/giftcard')->getCollection()
			->addFieldToFilter('giftcard_id', $id)
			->getFirstItem();
		return $mdl;
	}
	
	public function getTrueOrderNumber($id)
	{
		$mdl = Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('entity_id', $id)
			->getFirstItem();
		return $mdl->getIncrementId();
	}
	
	public function getHistory($id) 
	{
		$mdl = Mage::getModel('giftcard/payment')->getCollection()
			->addFieldToFilter('giftcard_id', $id)
			->setOrder('created_at', 'ASC');
		return $mdl;
	}
	public function getTransType($typeId = null)
	{
		if($typeId) {
			return 'Refill';
		} else {
			return 'Charge';
		}
	}
}