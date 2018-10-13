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
 * @version		0.1.1
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
    
}
