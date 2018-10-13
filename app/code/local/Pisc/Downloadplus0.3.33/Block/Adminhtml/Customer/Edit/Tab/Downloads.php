<?php
/**
 * Customer Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.1
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Edit_Tab_Downloads extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _construct()
    {
        parent::_construct();
		$this->setTemplate('downloadplus/customer/edit/downloads.phtml');
    }

    protected function _getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}

    public function getTabLabel()
    {
        return Mage::helper('downloadplus')->__('Current Downloads');
    }

    public function getTabTitle()
    {
        return Mage::helper('downloadplus')->__('Show current customer related downloads');
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
        return $this->getUrl('downloadplusadmin/customer_edit/currentDownloads', array('_current' => true));
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
		Mage::register('downloadplus_adminhtml_customer_edit_tab_downloads_form_elements', true);
		
		$accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
										->setId('downloadplusCustomerDownloadsPurchased');

		$accordion->addItem('downloadplus_customer_downloads_purchased', array(
            'title'   => 'Purchased Downloadable Products',
            'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_customer_edit_tab_downloads_purchasedlinks')->toHtml(),
            'open'    => true
		));

		$accordion->addItem('downloadplus_customer_downloads_serialnumbers', array(
            'title'   => 'Serial Numbers for Purchased Downloadable Products',
            'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_customer_edit_tab_downloads_serialnumbers')->toHtml(),
            'open'    => false
		));

		$accordion->addItem('downloadplus_customer_downloads_additional', array(
            'title'   => 'Additional Downloads for this Customer',
            'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_customer_edit_tab_downloads_additional')->toHtml(),
            'open'    => false
		));

		$this->setChild('accordion-downloads-additional', $accordion);

		return parent::_toHtml();
	}

}