<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class TinyBrick_GiftCard_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Totals
{
	public function _toHtml()
	{
		$str = Mage::app()->getFrontController()->getRequest()->getPathInfo();
		//if($str == '/checkout/onepage/savePayment/') {
    	if($str == '/sourcecode/onepage/saveSourcecode/') {  // added by krishinc developer to show gift card in total section of onepage checkout
    		$this->setTemplate('checkout/onepage/review/gc-totals.phtml');
    	}
		if (!$this->getTemplate()) {
            return '';
        }
        $html = $this->renderView();
        return $html;
	}
	
	public function getGiftCards()
	{
		$cards = Mage::getModel('giftcard/payment')->getCollection()
			->addFieldToFilter('quote_id', $this->getQuote()->getId());
		$cards->getSelect()
			->join(array('gc' => (string)Mage::getConfig()->getTablePrefix().'giftcard_entity'),
				'main_table.giftcard_id = gc.giftcard_id',
				array('number'));
		
		return $cards;
	}
}
