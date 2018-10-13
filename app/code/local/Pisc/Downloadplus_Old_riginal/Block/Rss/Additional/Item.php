<?php
/**
 * RSS Feed Entry block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.0
 */

class Pisc_Downloadplus_Block_Rss_Additional_Item extends Mage_Core_Block_Template
{

	protected $_download = null;

    protected function _construct()
    {
        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        $this->_download = Mage::registry('downloadplus_rss_additional_item');
    }

    public function getDownload()
    {
    	return $this->_download;
    }

    public function getProduct()
    {
    	if (isset($this->_download)) {
    		return Mage::getModel('catalog/product')->load($this->_download->getProductId());
    	}
    	return null;
    }

    public function getDetail()
    {
    	if (isset($this->_download)) {
    		return $this->_download->getDetail();
    	}
    	return null;
    }

}
