<?php
/**
 * Catalog Product Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.1
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloads_Additional extends Mage_Adminhtml_Block_Template
{

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('downloadplus/product/edit/downloads/additional.phtml');
	}

	/*
     * Returns the current Customer
     */
    public function getProduct()
    {
		return Mage::registry('current_product');
    }

	/**
	 * Retrieve Add button HTML
	 */
	public function getAddButtonHtml()
	{
		$addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
						->setData(array(
				                'label' => Mage::helper('downloadable')->__('Add New Row'),
				                'id' => 'add_product_link_item',
				                'class' => 'add',
						));
		return $addButton->toHtml();
	}

	/**
	 * Return array of links
	 */
	public function getLinkData()
	{
		Mage::getModel('downloadplus/product_download');
		
		$linkArr = array();

		$download = Mage::getModel('downloadplus/link_product_item');
		$collection = $download->getCollection()->getByProductId($this->getProduct()->getId());
		foreach ($collection as $item) {
			$tmpLinkItem = Array(
				'link_id' => $item->getId(),
				'title' => $item->getLinkTitle(),
				'description' => $item->getDownloadDetail()->getDetail(),
				'link_url' => $item->getLinkUrl(),
				'link_type' => $item->getLinkType(),
				'sort_order' => $item->getSortOrder()
			);

			$file = Mage::helper('downloadable/file')->getFilePath(Pisc_Downloadplus_Model_Product_Download::getBasePath(), $item->getLinkFile());
			if ($item->getLinkFile() && is_file($file)) {
				$name = '<a href="' . $this->getUrl('downloadplusadmin/product_file/link', array('id' => $item->getId(), '_secure' => true)) . '">' .
						Mage::helper('downloadable/file')->getFileFromPathFile($item->getLinkFile()) .
						'</a>';

				$tmpLinkItem['file_save'] = Array(Array(
												 'file' => $item->getLinkFile(),
												 'name' => $name,
												 'size' => filesize($file),
												 'status' => 'old'
												 ));
			 }

			 $linkArr[] = new Varien_Object($tmpLinkItem);
		}

		return $linkArr;
	}

	/**
	 * Prepare block Layout
	 */
	protected function _prepareLayout()
	{
		$this->setChild(
            'upload_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
			->addData(array(
	                    'id'      => '',
	                    'label'   => Mage::helper('adminhtml')->__('Upload Files'),
	                    'type'    => 'button',
	                    'onclick' => 'Downloadable.massUploadByType(\'productlinks\')'
	                    ))
                    );
	}

	/**
	 * Retrieve Upload button HTML
	 */
	public function getUploadButtonHtml()
	{
		return $this->getChild('upload_button')->toHtml();
	}

	/**
	 * Retrive config json
	 */
	public function getConfigJson($type='links')
	{
		$this->getConfig()->setUrl(Mage::getModel('adminhtml/url')
									->addSessionParam()
									->getUrl('downloadplusadmin/product_file/upload',
										Array(
											'type' => $type,
											'product_id' => $this->getProduct()->getId(),
										'_secure' => true)));
		$this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
		$this->getConfig()->setFileField($type);
		$this->getConfig()->setFilters(array(
            'all'    => array(
		                'label' => Mage::helper('adminhtml')->__('All Files'),
		                'files' => array('*.*')
						)
		));
		$this->getConfig()->setReplaceBrowseWithRemove(true);
		$this->getConfig()->setWidth('32');
		$this->getConfig()->setHideUploadButton(true);
		return Zend_Json::encode($this->getConfig()->getData());
	}

	/**
	 * Retrive config object
	 */
	public function getConfig()
	{
		if(is_null($this->_config)) {
			$this->_config = new Varien_Object();
		}

		return $this->_config;
	}

}
