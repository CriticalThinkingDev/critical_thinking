<?php
/**
 * Downloadplus Product Links part block
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Catalog_Product_Links extends Mage_Downloadable_Block_Catalog_Product_Links
{
	
    public function getLinks($attributes=true)
    {
    	$links = parent::getLinks();
    	
    	if ($attributes) {
			$helper = Mage::helper('downloadplus');
    		foreach ($links as $key=>$link) {
    			if ($html = $helper->getLinkAttributesHtml($link)) {
    				$link->setTitle($link->getTitle().$html);
    				$link->setLinkTitle($link->getTitle());
    				$links[$key] = $link;
    			}
    		}
    	}
    	
    	return $links;
    }

}
