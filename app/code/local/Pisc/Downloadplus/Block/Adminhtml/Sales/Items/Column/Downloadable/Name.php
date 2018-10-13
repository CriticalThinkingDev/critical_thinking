<?php
/**
 * Downloadplus Sales Order downloadable items name column renderer
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.1
 */

class Pisc_Downloadplus_Block_Adminhtml_Sales_Items_Column_Downloadable_Name extends Mage_Downloadable_Block_Adminhtml_Sales_Items_Column_Downloadable_Name
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

    public function setTemplate($template)
    {
    	$parts = explode(DS, $template);
    	if (isset($parts[0])) {
    		$parts[0] = 'downloadplus';
    		$file = Mage::getBaseDir('design').DS.'adminhtml'.DS.'default'.DS.'default'.DS.implode(DS, $parts);
    		if (file_exists($file)) {
    			$template = implode(DS, $parts);
    		}
    	}
    	return parent::setTemplate($template);
    }
    
    public function addColumnRender($column, $block, $template, $type=null)
    {
    	$parts = explode('/', $template);
    	if (isset($parts[1])) {
    		$file = Mage::getBaseDir('design').DS.'adminhtml'.DS.'default'.DS.'default'.DS.str_replace('_', DS, $parts[1]).'.phtml';
    		if (file_exists($file)) {
    			$template = 'downloadplus/'.$parts[1];
    		}
    	}
    	return parent::addColumnRender($column, $block, $template, $type);
    }
    
}
?>