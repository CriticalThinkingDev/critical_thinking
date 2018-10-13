<?php
/**
 * Customer Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Detail extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	protected $_currentFiles = null;
	protected $_historicalFiles = null;
	protected $_availableFiles = null;

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('downloadplus/product/edit/detail.phtml');
	}

	protected function _getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}

	public function getTabLabel()
	{
		return Mage::helper('downloadplus')->__('Downloadable Details');
	}

	public function getTabTitle()
	{
		return Mage::helper('downloadplus')->__('Edit Details of Downloadable Products');
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
		return $this->getUrl('adminhtml/downloadplus_product_edit/downloadDetail', array('_current' => true));
	}

    public function getAjaxUrl()
    {
    	return $this->getUrl('adminhtml/downloadplus_ajax');
    }

	public function canShowTab()
	{
		return true;
	}

	public function isHidden()
	{
		return false;
	}

	public function getStore()
	{
		return $this->getRequest()->getParam('store', 0);
	}

	public function getCurrentFiles()
	{
		if (!$this->_currentFiles) {
			$product = Mage::registry('current_product');
			$this->_currentFiles = Mage::getModel('downloadplus/download_detail')
									->getCollection()
									->addProductToFilter($product)
									->addStoreToFilter($this->getStore())
									->getActiveFiles();
		}
		return $this->_currentFiles;
	}

	public function getHistoricalFiles()
	{
		if (!$this->_historicalFiles) {
			$product = Mage::registry('current_product');
			$this->_historicalFiles = Mage::getModel('downloadplus/download_detail')
										->getCollection()
										->addProductToFilter($product)
										->addStoreToFilter($this->getStore())
										->addSort('file ASC')
										->getHistoricalFiles();
		}
		return $this->_historicalFiles;
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		// Current downloadable files
		$accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
										->setId('downloadplusDetailCurrent');

		$files = $this->getCurrentFiles();

		Mage::unregister('downloadplus_detail_area');
		Mage::unregister('downloadplus_detail');
		Mage::unregister('downloadplus_detail_key');

		Mage::register('downloadplus_detail_area', 'active');
		foreach ($files as $key=>$file) {
			// Load detail by filename to also get store-related data
			Mage::register('downloadplus_detail', $file);
			if ($file->getFile()) {
				$accordion->addItem($file->getFile(), array(
		            'title'   => basename($file->getFile()),
					'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_file')->toHtml(),
		            'open'    => false
				));
			}
			Mage::unregister('downloadplus_detail');
		}
		Mage::unregister('downloadplus_detail_area');

		$this->setChild('accordion-current', $accordion);

		// Historical downloadable files
		$accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
										->setId('downloadplusDetailHistorical');

		$files = $this->getHistoricalFiles();

		Mage::unregister('downloadplus_detail_area');
		Mage::unregister('downloadplus_detail');

		Mage::register('downloadplus_detail_area', 'historical');
		foreach ($files as $key=>$file) {
			// Load detail by filename to also get store-related data
			Mage::register('downloadplus_detail', $file);
			if ($file->getFile()) {
				$accordion->addItem($file->getFile(), array(
		            'title'   => basename($file->getFile()),
					'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_file')->toHtml(),
		            'open'    => false,
					'class'   => ($file->isHidden())?'hidden':''
				));
			}
			Mage::unregister('downloadplus_detail');
		}
		Mage::unregister('downloadplus_detail_area');

		$this->setChild('accordion-historical', $accordion);

		// Add other available files
		$accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
										->setId('downloadplusDetailAddHistorical');

		$accordion->addItem('add_historical_link_files', array(
            'title'   => 'Links',
            'ajax'	  		=> true,
			'content_url'	=> $this->getUrl('adminhtml/downloadplus_product_edit/viewAvailableLinks', array('_current' => true)),
            //'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_availablelinks')
			//						->setId('downloadplusAddHistoricalLinkFiles')
			//						->toHtml(),
            'open'    => false
		));

		$accordion->addItem('add_historical_linksample_files', array(
            'title'   => 'Link Samples',
	        'ajax'	  		=> true,
			'content_url'	=> $this->getUrl('adminhtml/downloadplus_product_edit/viewAvailableLinksamples', array('_current' => true)),
			//'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_availablelinksamples')
			//						->setId('downloadplusAddHistoricalLinkSampleFiles')
			//						->toHtml(),
            'open'    => false
		));

		$accordion->addItem('add_historical_sample_files', array(
            'title'   => 'Samples',
	        'ajax'	  		=> true,
			'content_url'	=> $this->getUrl('adminhtml/downloadplus_product_edit/viewAvailableSamples', array('_current' => true)),
			//'content' => $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_availablesamples')
			//						->setId('downloadplusAddHistoricalSampleFiles')
			//						->toHtml(),
            'open'    => false
		));

		$this->setChild('accordion-add-other-historical', $accordion);

		return parent::_toHtml();
	}

}