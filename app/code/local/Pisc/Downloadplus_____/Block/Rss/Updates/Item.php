<?php
/**
 * RSS Feed Entry block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.0
 */

class Pisc_Downloadplus_Block_Rss_Updates_Item extends Mage_Core_Block_Template
{

	protected $_update = null;

    protected function _construct()
    {
        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        $this->_update = Mage::registry('downloadplus_rss_updates_item');
    }

    public function getUpdate()
    {
    	return $this->_update;
    }

    public function getProduct()
    {
    	if (isset($this->_update['product'])) {
    		return $this->_update['product'];
    	}
    	return null;
    }

    public function getDetail()
    {
    	if (isset($this->_update['detail'])) {
    		return $this->_update['detail'];
    	}
    	return null;
    }

}
