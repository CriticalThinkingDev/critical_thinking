<?php
/**
 * Customer Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Settings extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	protected $_currentFiles = null;
	protected $_historicalFiles = null;
	protected $_availableFiles = null;

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('downloadplus/product/edit/settings.phtml');
	}

	protected function _getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}

	public function getTabLabel()
	{
		return Mage::helper('downloadplus')->__('Downloadable Settings');
	}

	public function getTabTitle()
	{
		return Mage::helper('downloadplus')->__('Allows additional settings for the downloadable links');
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
		return $this->getUrl('downloadplusadmin/product_edit/settings', array('_current' => true));
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

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		// Current downloadable files
		$product = Mage::registry('current_product');

		$accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
										->setId('downloadplusSettingsLinks');
		$accordion->addItem('settings_links', array(
            'title'   => Mage::helper('downloadplus')->__('Additional settings for Downloadable Links'),
			'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_settings_links')->toHtml(),
            'open'    => true
		));

		$this->setChild('accordion-links', $accordion);
		
		return parent::_toHtml();
	}

}