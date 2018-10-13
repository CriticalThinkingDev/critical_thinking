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

	/*
	 * Return attributes
	*/
	public function getLinkAttributes($id, $name)
	{
		$helper = Mage::helper('downloadplus');
		 
		$block = $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_downloadable_links_attributes', 'downloadplus_product_link_attributes');
		$block->setName($name);
		$block->setId($id);
		 
		$block->setAttributes($helper->getCustomDownloadableAttributes($this->getProduct(), 'links'));
		 
		$result = $block->toJSHtml();
		return $result;
	}
	
	public function hasLinkAttributes()
	{
		$helper = Mage::helper('downloadplus');
		return $helper->hasCustomDownloadableAttributes($this->getProduct(), 'link');
	}
	
	/**
	 * Return array of links
	 */
	public function getLinkData()
	{
		Mage::getModel('downloadplus/product_download');
		Mage::helper('downloadplus/download');
		
		$linkArr = array();

		$download = Mage::getModel('downloadplus/link_product_item');
		$collection = $download->getCollection()->getByProductId($this->getProduct()->getId());
		foreach ($collection as $item) {
			$item->setStoreId(Mage::app()->getRequest()->getParam('store'));
			$tmpLinkItem = Array(
				'link_id' => $item->getId(),
				'title' => $item->getLinkTitle(),
				'store_title' => $item->getStoreTitle(),
				'description' => $item->getDescription(),
				'store_description' => $item->getStoreDescription(),
				'link_url' => $item->getLinkUrl(),
				'link_type' => $item->getLinkType(),
				'amazon_s3_object' => null,
				'amazon_s3_bucket' => null,
				'amazon_s3_file' => null,
				'amazon_cf_object' => null,
				'sort_order' => $item->getSortOrder(),
				'attributes' => $item->getAttributes(true),
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

			 if ($item->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3) {
			 	$tmpLinkItem['amazon_s3_object'] = $item->getData('link_url');
			 
			 	$object = explode('|', $tmpLinkItem['amazon_s3_object']);
			 	if (isset($object[0])) {
			 		$tmpLinkItem['amazon_s3_bucket'] = $object[0];
			 	}
			 	if (isset($object[1])) {
			 		$tmpLinkItem['amazon_s3_file'] = $object[1];
			 	}
			 	 
			 	$tmpLinkItem['link_url'] = null;
			 }
			 if ($item->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF) {
			 	$tmpLinkItem['amazon_cf_object'] = $item->getData('link_url');
			 	$tmpLinkItem['link_url'] = null;
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

	/*
	 * Return File Selection for AmazonS3 content (requires DownloadPlusAWS)
	 */
	public function getAmazonS3ObjectsSelect($id, $name)
	{
		$html = '';
		 
		if (Mage::helper('downloadplus')->existsDownloadplusAWS()) {
	
			$config = Mage::getModel('downloadplusaws/config')->setStore();
			 
			if ($config->isAdminLinkStyleObjects()) {
				if (!$block = $this->getLayout()->getBlock('downloadplus_amazon_s3_link')) {
					$block = $this->getLayout()->createBlock('downloadplusaws/adminhtml_amazon_s3_link', 'downloadplus_amazon_s3_link');
				}
				if ($block) {
					$html = $block->getObjectsSelect($id, $name, $this->isDisabled())->toHtml();
				}
			}
	
		}
		 
		return $html;
	}
	
	/*
	 * Return File Selection for AmazonS3 content (requires DownloadPlusAWS)
	 */
	public function getAmazonS3BucketsSelect($id, $name)
	{
		$html = '';
		 
		if (Mage::helper('downloadplus')->existsDownloadplusAWS()) {
	
			$config = Mage::getModel('downloadplusaws/config')->setStore(Mage::helper('downloadplus')->getStore());
	
			if ($config->isAdminLinkStyleBuckets()) {
				if (!$block = $this->getLayout()->getBlock('downloadplus_amazon_s3_link')) {
					$block = $this->getLayout()->createBlock('downloadplusaws/adminhtml_amazon_s3_link', 'downloadplus_amazon_s3_link');
				}
				if ($block) {
					$html = $block->getBucketsSelect($id, $name, $this->isDisabled())->toHtml();
				}
			}
	
		}
		 
		return $html;
	}
	
	/*
	 * Return File Selection for AmazonS3 content (requires DownloadPlusAWS)
	 */
	public function getAmazonCloudfrontObjectsSelect($id, $name)
	{
		$html = '';
		 
		if (Mage::helper('downloadplus')->existsDownloadplusAWS()) {
	
			if (!$block = $this->getLayout()->getBlock('downloadplus_amazon_cloudfront_link')) {
				$block = $this->getLayout()->createBlock('downloadplusaws/adminhtml_amazon_cloudfront_link', 'downloadplus_amazon_cloudfront_link');
			}
			if ($block) {
				$html = $block->getObjectsSelect($id, $name, $this->isDisabled())->toHtml();
			}
	
		}
		 
		return $html;
	}

	public function isDisabled()
	{
		/*
		if (Mage::app()->getRequest()->getParam('store')) {
			return ' disabled="disabled" ';
		}
		*/
		return null;
	}
	
	public function toJSHtml($html)
	{
		// Remove line-breaks for use in JavaScript;
		$html = str_replace(array("\r\n", "\r", "\n", "\t"), '', $html);
		return $html;
	}
	
	public function getTestUrlAction()
	{
		return Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('downloadplusadmin/ajax/testUrl', array('_secure' => true));
	}
	
}
