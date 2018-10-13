<?php
/**
 * Downloadplus Shopping cart downloadable item render block
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Checkout_Cart_Item_Renderer extends Mage_Downloadable_Block_Checkout_Cart_Item_Renderer
{

    public function getLinks($attributes=true)
    {
        $itemLinks = parent::getLinks();
        
        if ($attributes) {
	        $helper = Mage::helper('downloadplus');
	        foreach ($itemLinks as $item) {
				if ($html = $helper->getLinkAttributesHtml($item)) {
					$item->setTitle($item->getTitle().$html);				
				}
	        }
        }
        
        return $itemLinks;
    }

}