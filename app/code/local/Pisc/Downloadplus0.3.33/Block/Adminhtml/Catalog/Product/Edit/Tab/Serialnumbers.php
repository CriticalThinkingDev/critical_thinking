<?php
/**
 * Customer Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Serialnumbers extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	protected $_currentFiles = null;
	protected $_historicalFiles = null;
	protected $_availableFiles = null;

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('downloadplus/product/edit/serialnumbers.phtml');
	}

	protected function _getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}

	public function getTabLabel()
	{
		return Mage::helper('downloadplus')->__('Serialnumbers');
	}

	public function getTabTitle()
	{
		return Mage::helper('downloadplus')->__('Add Serialnumbers to Products');
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
		return $this->getUrl('downloadplusadmin/product_edit/serialnumbers', array('_current' => true));
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
										->setId('downloadplusSerialnumbersImport');
		$accordion->addItem('serialnumbers_import', array(
            'title'   => Mage::helper('downloadplus')->__('Add Serialnumbers to this Product'),
			'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_serialnumbers_import')->toHtml(),
            'open'    => true
		));

		$accordion->addItem('serialnumbers_list_available', array(
            'title'   => Mage::helper('downloadplus')->__('Serialnumbers available for this Product'),
	        'ajax'	  		=> true,
			'content_url'	=> $this->getUrl('downloadplusadmin/product_edit/serialnumbersAvailable', array('_current' => true)),
			//'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_availablelinksamples')
			//						->setId('downloadplusAddHistoricalLinkSampleFiles')
			//						->toHtml(),
            'open'    => false
		));

		$this->setChild('accordion-import', $accordion);

		return parent::_toHtml();
	}

}