<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable link purchased item model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.7
 */

class Pisc_Downloadplus_Model_Link_Purchased_Item extends Mage_Downloadable_Model_Link_Purchased_Item
{
	
	protected $_eventPrefix = 'downloadplus_link_purchased_item';

	protected function _construct()
    {
        parent::_construct();
        $this->_init('downloadplus/link_purchased_item');
    }

    public function getLink()
    {
    	if ($this->getLinkId()) {
    		return Mage::getModel('downloadplus/link')->load($this->getLinkId());
    	}
    	return null;
    }
    
    public function getExtension()
    {
    	return Mage::getModel('downloadplus/link_purchased_item_extension')->loadByPurchasedLink($this);
    }

    public function getLinkPurchased()
    {
    	$result = Mage::getModel('downloadable/link_purchased');
    	if ($this->getPurchasedId()) {
   			$result->load($this->getPurchasedId());
    	}
    	return $result;
    }
    
    public function getOrder()
    {
    	$result = Mage::getModel('sales/order');
    	if ($this->getPurchasedId()) {
    		$result->load($this->getLinkPurchased()->getOrderId());
    	}
    	return $result;
    }
    
    public function getOrderItem()
    {
    	$result = Mage::getModel('sales/order_item');
    	if ($this->getOrderItemId()) {
    		$result->load($this->getOrderItemId());
    	}
    	return $result;
    }

    public function getStatus()
    {
		$extension = $this->getExtension();
		if ($extension->getId() && $extension->isExpired()) {
			return self::LINK_STATUS_EXPIRED;
		}
		return parent::getStatus();
    }
    
    /*
     * Sets Resource Model flag to disable Foreign Key Checks on Save;
     */
    public function disableForeignKeyCheck()
    {
    	$this->getResource()->disableForeignKeyCheck();
    	return $this;
    }

    public function getLinkTitle($attributes = false) 
    {
    	if ($attributes) {
    		$helper = Mage::helper('downloadplus');
   			if ($html = $helper->getLinkAttributesHtml($this)) {
   				return $this->getData('link_title').$html;
    		} else {
    			return parent::getLinkTitle();
    		}
    	}
    	return parent::getLinkTitle();
    }

    public function getCurrentLinkTitle($attributes = false)
    {
        if (Mage::helper('downloadplus')->existsDownloadplusRepository()) {
            if (Mage::helper('downloadplusrepository')->isProductRepositoryStyle($this->getProduct())) {
                $title = Mage::getModel('downloadplus/link_title_history')->getCurrentTitle($this, $this->getLinkTitle());
                if ($attributes) {
                    $helper = Mage::helper('downloadplus');
                    if ($html = $helper->getLinkAttributesHtml($this)) {
                        $title.= $html;
                    }
                }
                return $title;
            }
        }
        return $this->getLinkTitle($attributes);        
    }
    
    public function getAttributes()
    {
    	$product = Mage::getModel('catalog/product')->load($this->getProductId());
    	$attributes = Mage::app()->getHelper('downloadplus')->getCustomDownloadableAttributes($product, 'link');
    	$extension = $this->getExtension();
    	 
    	foreach ($attributes as $attribute) {
    		$attribute->setValue($extension->getAttribute($attribute->getAttributeCode()));
    	}
    	 
    	return $attributes;
    }
    
    public function getAttribute($code, $default=null)
    {
    	$result = null;
    	$attributes = $this->getAttributes();
    	if (isset($attributes[$code])) {
    		$result = $attributes[$code]->getValue();
    	} else {
    		$result = $default;
    	}
    
    	return $result;
    }

    public function getProduct()
    {
        return Mage::getModel('catalog/product')->load($this->getProductId());            
    }
    
    public function getImageUrl()
    {
		return Mage::getModel('downloadplus/link_image')->setLink($this->getLink())->getImageUrl();
    }
    
    public function getImageThumbnailUrl($width=null, $height=null)
    {
    	return Mage::getModel('downloadplus/link_image')->setLink($this->getLink())->getImageThumbnailUrl($width, $height);
    }

    public function _beforeSave()
    {
        if (is_null($this->getOrderItemId())) {
            throw new Exception(
                Mage::helper('downloadable')->__('Order item id cannot be null'));
        }
            
        if (method_exists($this, 'isObjectNew') && !$this->getId()) {
            $this->isObjectNew(true);
        }
        Mage::dispatchEvent('model_save_before', array('object'=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_save_before', $this->_getEventData());
        return $this;
    }
    
}
