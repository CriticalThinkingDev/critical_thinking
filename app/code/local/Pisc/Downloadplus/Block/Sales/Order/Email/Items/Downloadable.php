<?php
/**
 * Downloadplus Sales Order Email items renderer
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Sales_Order_Email_Items_Downloadable extends Mage_Downloadable_Block_Sales_Order_Email_Items_Downloadable
{

	public function getLinks($attributes=true)
    {
    	$purchasedLinks = parent::getLinks()->getPurchasedItems();

    	if ($attributes) {
	    	$helper = Mage::helper('downloadplus');
	    	foreach ($purchasedLinks as $purchasedLink) {
	    		if ($html = $helper->getLinkAttributesHtml($purchasedLink)) {
	    			$purchasedLink->setLinkTitle($purchasedLink->getLinkTitle().$html);
	    		}
	    	}
    	}
    	
    	$this->_purchased->setPurchasedItems($purchasedLinks);
        return $this->_purchased;
    }

}