<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloadable Link Item Extension model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.3
 */

class Pisc_Downloadplus_Model_Link_Purchased_Item_Extension extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_link_purchased_item_extension';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        //parent::_construct();
        $this->_init('downloadplus/link_purchased_item_extension');
        $this->_setResourceModel('downloadplus/link_purchased_item_extension', 'downloadplus/link_purchased_item_extension_collection');
    }

    /*
     * Initializes specific Data
     */
    public function initialize($itemId=null)
    {
    	$this->setData('id', null);
    	$this->setData('item_id', $itemId);
    	$this->setData('expires_on', null);
    	return $this;
    }

    /*
     * Sets expiration date either with date or with days from current date
     */
    public function setExpiresOn($value)
    {
		if ($this->checkDate($value)) {
			$this->setData('expires_on', date("Y-m-d", strtotime($value)));
		} else {
			if (is_numeric($value)) {
				$date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
				$date = date("Y-m-d", strtotime('+'.$value.' days', strtotime($date)));
				$this->setData('expires_on', $date);
			}
		}
		return $this;
    }
        
    /*
     * Creates a unconditional expiration
     */
    public function createExpiration($purchasedLink)
    {
    	$this->loadByPurchasedLink($purchasedLink);
    	if (is_null($this->getExpiresOn())) {
    		$extension = Mage::getModel('downloadplus/link_extension');
    		$extension->loadByLinkId($purchasedLink->getLinkId());
    		if ($extension->getId()&& !is_null($extension->getExpiry())) {
    			$this->setItemId($purchasedLink->getItemId());
    			$date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
    			$date = date("Y-m-d", strtotime('+'.$extension->getExpiry().' days', strtotime($date)));
    			$this->setExpiresOn($date);
    			$this->save();
    		}
    	}
    	return $this;
    }

    /*
     * Creates a expiration only when set to onOrder
     */
    public function createExpirationOnOrder($purchasedLink)
    {
    	$this->loadByPurchasedLink($purchasedLink);
    	if (is_null($this->getExpiresOn())) {
    		$extension = Mage::getModel('downloadplus/link_extension');
    		$extension->loadByLinkId($purchasedLink->getLinkId());
    		if ($extension->getId() && $extension->isExpireOnOrder() && !is_null($extension->getExpiry())) {
    			$this->setItemId($purchasedLink->getItemId());
    			$date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
    			$date = date("Y-m-d", strtotime('+'.$extension->getExpiry().' days', strtotime($date)));
    			$this->setExpiresOn($date);
    			$this->save();
    		}
    	}
    	return $this;
    }

    /*
     * Creates a expiration only when set to onDownload
     */
    public function createExpirationOnDownload($purchasedLink)
    {
    	$this->loadByPurchasedLink($purchasedLink);
    	if (is_null($this->getExpiresOn())) {
    		$extension = Mage::getModel('downloadplus/link_extension');
    		$extension->loadByLinkId($purchasedLink->getLinkId());
    		if ($extension->getId() && $extension->isExpireOnDownload() && !is_null($extension->getExpiry())) {
    			$this->setItemId($purchasedLink->getItemId());
    			$date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
    			$date = date("Y-m-d", strtotime('+'.$extension->getExpiry().' days', strtotime($date)));
    			$this->setExpiresOn($date);
    			$this->save();
    		}
    	}
    	return $this;
    }

    /*
     * Refreshes Expiration
     */
    public function refreshExpiration($purchasedLink)
    {
    	$extension = Mage::getModel('downloadplus/link_extension');
    	$extension->loadByLinkId($purchasedLink->getLinkId());
    	if ($extension->getId()) {
    		if ($extension->isExpireOnOrder() && !is_null($extension->getExpiry())) {
	    		$this->setItemId($purchasedLink->getItemId());
	    		$date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
	    		$date = date("Y-m-d", strtotime('+'.$extension->getExpiry().' days', strtotime($date)));
	    		$this->setExpiresOn($date);
	    		$this->save();
    		}
    		if ($extension->isExpireOnDownload() && !is_null($extension->getExpiry())) {
    			$this->setItemId($purchasedLink->getItemId());
    			$date = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
    			$date = date("Y-m-d", strtotime('+'.$extension->getExpiry().' days', strtotime($date)));
    			$this->setExpiresOn($date);
    			$this->save();
    		}
    		return $this;
    	}
    }
    
    /*
     * Returns days until expiration
     */
    public function getDaysUntilExpiration($expires_on=null)
    {
    	$result = null;
    	if (is_null($expires_on)) {
    		$expires_on = strtotime($this->getExpiresOn());
    	} else {
    		$expires_on = strtotime($expires_on);
    	}
    	if ($expires_on) {
    		$today = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));
    		$expires = new Zend_Date($expires_on);
    		$result = $expires->sub($today);
    		if ($result instanceof Zend_Date) {
    			$result = $result->getTimestamp();
    		}
    		$result = ceil($result/60/60/24);
    	} 
    	return $result;
    }
    
    /*
     * Returns boolean if expired
     */
    public function isExpired()
    {
    	$result = true;
    	$expires = $this->getDaysUntilExpiration();
    	if (is_null($expires)) {
    		$result = false;
    	} else {
    		$result = ($expires<0);
    	}
    	return $result;
    }
    
    /*
     * Loads by PurchasedLink
     */
    public function loadByPurchasedLink($purchasedLink)
    {
    	if ($purchasedLink instanceof Mage_Downloadable_Model_Link_Purchased_Item) {
    		$this->loadByItemId($purchasedLink->getItemId());
    	} else {
    		$this->initialize();
    	}
    	return $this;
    }
    
    /*
     * Loads the extension by Link ItemId
     */
    public function loadByItemId($id)
    {
    	if ($id) {
	        if ($result = $this->getResource()->getIdByItemId($id)) {
	        	$this->load($result);
	        } else {
	        	$this->initialize($id);
	        }
    	} else {
    		$this->initialize();
    	}
    	return $this;
    }

    /*
     * Returns the associated Link Purchsed Item
     */
    public function getLinkPurchasedItem()
    {
    	$object = Mage::getModel('downloadplus/link_purchased_item');
    	if ($id = $this->getData('item_id')) {
    		$object->load($id);
    	}
    	return $object;
    }
    
    /*
     * Saves the Data of the Model and the related Models
     */
    public function save()
    {
    	parent::save();
    	return $this;
    }

    /*
     * Deletes the Data of this Model and the related Models
     */
    public function delete()
    {
    	parent::delete();
       	return $this;
    }

    /*
     * Checks if a date is valid
     */
    public function checkDate($dateStr)
    {
    	$stamp = strtotime($dateStr);
    	if ($stamp) {
	    	$month = date('m', $stamp);
	    	$day   = date('d', $stamp);
	    	$year  = date('Y', $stamp);
	    	return checkdate($month, $day, $year);
    	}
    	return false;
    }
        
}
