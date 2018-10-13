<?php
/**
 * DownloadplusBuilder Event Output Block
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusBuilder
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Sales_Order_Item_Renderer_Downloadable extends Mage_Downloadable_Block_Sales_Order_Item_Renderer_Downloadable
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
    	 
    	$this->_purchasedLinks->setPurchasedItems($purchasedLinks);
        return $this->_purchasedLinks;
    }

}