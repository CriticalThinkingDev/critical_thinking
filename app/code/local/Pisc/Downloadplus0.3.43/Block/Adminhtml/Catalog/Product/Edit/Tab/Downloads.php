<?php
/**
 * Catalog Product Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.1
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloads extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _construct()
    {
        parent::_construct();
		$this->setTemplate('downloadplus/product/edit/downloads.phtml');
    }

    protected function _getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}

    public function getTabLabel()
    {
        return Mage::helper('downloadplus')->__('Additional Downloads');
    }

    public function getTabTitle()
    {
        return Mage::helper('downloadplus')->__('Allows to add free downloads to the Product');
    }

    public function getTabClass()
    {
        return 'ajax only';
    }

    public function getClass()
    {
        return $this->getTabClass();
    }

    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/product_edit/additionalDownloads', array('_current' => true));
    }

    public function getAjaxUrl()
    {
    	return $this->getBaseUrl().'downloadplusadmin/ajax/';
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function isReadonly()
    {
    	return false;
    }
    
	/*
     * Returns the current Product
     */
    public function getProduct()
    {
		return Mage::registry('current_product');
    }

    public function getPostMaxSize()
    {
        return ini_get('post_max_size');
    }

    public function getUploadMaxSize()
    {
        return ini_get('upload_max_filesize');
    }

    public function getDataMaxSize()
    {
        return min($this->getPostMaxSize(), $this->getUploadMaxSize());
    }

    public function getDataMaxSizeInBytes()
    {
        $iniSize = $this->getDataMaxSize();
        $size = substr($iniSize, 0, strlen($iniSize)-1);
        $parsedSize = 0;
        switch (strtolower(substr($iniSize, strlen($iniSize)-1))) {
            case 't':
                $parsedSize = $size*(1024*1024*1024*1024);
                break;
            case 'g':
                $parsedSize = $size*(1024*1024*1024);
                break;
            case 'm':
                $parsedSize = $size*(1024*1024);
                break;
            case 'k':
                $parsedSize = $size*1024;
                break;
            case 'b':
            default:
                $parsedSize = $size;
                break;
        }
        return $parsedSize;
    }

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		// Add free downloads
    	$this->setChild('downloads-product',
                $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_downloads_additional')
        );

		return parent::_toHtml();
	}

}