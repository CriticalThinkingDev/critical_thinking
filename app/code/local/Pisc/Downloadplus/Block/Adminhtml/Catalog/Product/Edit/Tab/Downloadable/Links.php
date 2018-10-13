<?php
/**
 * Downloadplus Catalog Product Edit Tab Downloadable Links Block
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.6
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links extends Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadplus/product/edit/downloadable/links.phtml');
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

    	$this->setChild(
    			'upload_button',
    			$this->getLayout()->createBlock('adminhtml/widget_button')->addData(array(
					'id'      => '',
					'label'   => Mage::helper('adminhtml')->__('Upload Files'),
					'type'    => 'button',
					'onclick' => 'Downloadable.massUploadByType(\'links\');Downloadable.massUploadByType(\'linkssample\');Downloadable.massUploadByType(\'linksimage\');'
    			))
    	);
    }

    /*
     * Return link title array
     */
    public function getLinkTitleOptions($id, $name)
    {
    	$result = Array(
    					Array('value'=>null,'label'=>$this->__('- Use entry above -'))
    				);

    	$collection = Mage::getModel('downloadplus/link_title')->getCollection()
    						->addStoreToFilter(Mage::app()->getStore())
    						->getUniqueTitles();

    	foreach ($collection as $item) {
    		$result[]=Array('value'=>addslashes($item['title']),'label'=>addslashes($item['title']));
    	}

   		$block = $this->getLayout()->createBlock('core/html_select', 'downloadplus_link_title_options');
   		$block->setName($name);
   		$block->setId($id);
   		//$block->setTitle('');
   		$block->setClass('downloadplus_admin_link_title_options select');
   		$block->setOptions($result);
   		$result = $block->toHtml();

   		return $result;
    }

    /*
     * Return attributes
     */
    public function getLinkAttributes($id, $name)
    {
    	$helper = Mage::helper('downloadplus');

    	$block = $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_downloadable_links_attributes', 'downloadplus_link_attributes');
    	$block->setName($name);
    	$block->setId($id);

    	$block->setAttributes($helper->getCustomDownloadableAttributes($this->getProduct(), 'links'));

    	$result = $block->toJSHtml();
    	return $result;
    }

    public function getLinkBuildAttributes($id, $name)
    {
    	$result = '';
    	$helper = Mage::helper('downloadplus');
    	if ($helper->existsDownloadplusBuilder()) {
    		$block = $this->getLayout()->createBlock('downloadplusbuilder/adminhtml_catalog_product_edit_tab_downloadable_links_build_attributes', 'downloadplus_link_build_attributes');
    		$block->setName($name);
    		$block->setId($id);

    		$block->setAttributes($helper->getCustomDownloadableAttributes($this->getProduct(), 'build'));

    		$result = $block->toJSHtml();
    	}
    	return $result;
    }

    public function hasLinkAttributes()
    {
    	$helper = Mage::helper('downloadplus');
    	return $helper->hasCustomDownloadableAttributes($this->getProduct(), 'link');
    }

    public function hasLinkBuildAttributes()
    {
    	$helper = Mage::helper('downloadplus');
    	return $helper->hasCustomDownloadableAttributes($this->getProduct(), 'build');
    }

    private function _getLinkData()
    {
    	$linkArr = array();
    	$links = $this->getProduct()->getTypeInstance(true)->getLinks($this->getProduct());
    	$priceWebsiteScope = $this->getIsPriceWebsiteScope();
    	foreach ($links as $item) {
    		$tmpLinkItem = array(
    				'link_id' => $item->getId(),
    				'title' => $item->getTitle(),
    				'price' => $this->getPriceValue($item->getPrice()),
    				'number_of_downloads' => $item->getNumberOfDownloads(),
    				'is_shareable' => $item->getIsShareable(),
    				'link_url' => $item->getLinkUrl(),
    				'link_type' => $item->getLinkType(),
    				'sample_file' => $item->getSampleFile(),
    				'sample_url' => $item->getSampleUrl(),
    				'sample_type' => $item->getSampleType(),
    				'sort_order' => $item->getSortOrder()
    		);
    		$file = Mage::helper('downloadable/file')->getFilePath(
    			Pisc_Downloadplus_Model_Link::getBasePath($item->getLinkType()), $item->getLinkFile()
    		);
    		if ($item->getLinkFile() && is_file($file)) {
    			$name = '<a href="' . $this->getUrl('*/downloadable_product_edit/link', array('id' => $item->getId(), '_secure' => true)) . '">' .
    					Mage::helper('downloadable/file')->getFileFromPathFile($item->getLinkFile()) .
    					'</a>';
    			$tmpLinkItem['file_save'] = array(
    					array(
   							'file' => $item->getLinkFile(),
   							'name' => $name,
   							'size' => filesize($file),
   							'status' => 'old'
    					));
    		}
    		$sampleFile = Mage::helper('downloadable/file')->getFilePath(
    			Pisc_Downloadplus_Model_Link::getBaseSamplePath($item->getSampleType()), $item->getSampleFile()
    		);
    		if ($item->getSampleFile() && is_file($sampleFile)) {
    			$tmpLinkItem['sample_file_save'] = array(
    					array(
   							'file' => $item->getSampleFile(),
   							'name' => Mage::helper('downloadable/file')->getFileFromPathFile($item->getSampleFile()),
   							'size' => filesize($sampleFile),
   							'status' => 'old'
    					));
    		}

    		$imageFile = Mage::helper('downloadable/file')->getFilePath(
    			Pisc_Downloadplus_Model_Link_Image::getBasePath(), $item->getImageFile()
    		);
    		if ($item->getImageFile() && is_file($imageFile)) {
    			$tmpLinkItem['image_file_save'] = array(
    					array(
   							'file' => $item->getImageFile(),
   							'name' => Mage::helper('downloadable/file')->getFileFromPathFile($item->getImageFile()),
   							'size' => filesize($imageFile),
   							'status' => 'old'
    					));
    			$tmpLinkItem['image_preview'] = '<img src="'.$item->getImageUrl('thumbnail').'" />';
    		}

    		if ($item->getNumberOfDownloads() == '0') {
    			$tmpLinkItem['is_unlimited'] = ' checked="checked"';
    		}
    		if ($this->getProduct()->getStoreId() && $item->getStoreTitle()) {
    			$tmpLinkItem['store_title'] = $item->getStoreTitle();
    		}
    		if ($this->getProduct()->getStoreId() && $priceWebsiteScope) {
    			$tmpLinkItem['website_price'] = $item->getWebsitePrice();
    		}
    		$linkArr[] = new Varien_Object($tmpLinkItem);
    	}
    	return $linkArr;
    }

    /*
     * Return link data
     */
    public function getLinkData()
    {
    	Mage::helper('downloadplus/download');

    	$data = $this->_getLinkData();

    	foreach ($data as $item) {
    		$extension = Mage::getModel('downloadplus/link_extension')->loadByLinkId($item->getLinkId());
    		$item->setAttributes($extension->getAttributes());

    		$item->setData('use_default_price', $item->getData('price')==0);

    		$item->setData('amazon_s3_object', null);
    		$item->setData('amazon_s3_bucket', null);
    		$item->setData('amazon_s3_file', null);
    		$item->setData('amazon_cf_object', null);
    		$item->setData('file_local_object', null);
    		$item->setData('editionguard_object', null);
    		$item->setData('builder_object', null);
    		$item->setData('magazine_object', null);

    		$item->setData('sample_amazon_s3_object', null);
    		$item->setData('sample_amazon_s3_bucket', null);
    		$item->setData('sample_amazon_s3_file', null);
    		$item->setData('sample_amazon_cf_object', null);
    		$item->setData('sample_file_local_object', null);
    		$item->setData('sample_editionguard_object', null);

    		if ($item->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3) {
    			$item->setData('amazon_s3_object', $item->getData('link_url'));

    			$object = explode('|', $item->getData('amazon_s3_object'));
    			if (isset($object[0])) {
    				$item->setData('amazon_s3_bucket', $object[0]);
    			}
    			if (isset($object[1])) {
    				$item->setData('amazon_s3_file', $object[1]);
    			}

    			$item->unsetData('link_url');
    		}
    		if ($item->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF) {
    			$item->setData('amazon_cf_object', $item->getData('link_url'));
    			$item->unsetData('link_url');
    		}
    		if ($item->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
    			if ($file = $item->getData('file_save')) {
    				$item->setData('file_local_object', $file[0]['file']);
    				$item->unsetData('file_save');
    			}
    		}
    		if ($item->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD) {
    			$item->setData('editionguard_object', $item->getData('link_url'));
    			$item->unsetData('link_url');
    		}
    		if ($item->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_BUILDER) {
    			$item->setData('builder_object', $item->getData('link_url'));
    			$item->unsetData('link_url');
    		}
    		if ($item->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_MAGAZINE) {
    			$item->setData('magazine_object', $item->getData('link_url'));
    			$item->unsetData('link_url');
    		}

    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3) {
    			$item->setData('sample_amazon_s3_object', $item->getData('sample_url'));

    			$object = explode('|', $item->getData('sample_amazon_s3_object'));
    			if (isset($object[0])) {
    				$item->setData('sample_amazon_s3_bucket', $object[0]);
    			}
    			if (isset($object[1])) {
    				$item->setData('sample_amazon_s3_file', $object[1]);
    			}

    			$item->unsetData('sample_url');
    		}
    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF) {
    			$item->setData('sample_amazon_cf_object', $item->getData('sample_url'));
    			$item->unsetData('sample_url');
    		}
    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
    			if ($file = $item->getData('sample_file_save')) {
    				$item->setData('sample_file_local_object', $file[0]['file']);
    				$item->unsetData('sample_file_save');
    			}
    		}
    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD) {
    			$item->setData('sample_editionguard_object', $item->getData('sample_url'));
    			$item->unsetData('sample_url');
    		}

    		$item->setData('title', addslashes($item->getData('title')));
    	}

    	return $data;
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
	    			$html = $block->getObjectsSelect($id, $name)->toHtml();
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
	    			$html = $block->getBucketsSelect($id, $name)->toHtml();
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
    			$html = $block->getObjectsSelect($id, $name)->toHtml();
    		}

    	}

    	return $html;
    }

    /*
     * Return File Selection for AmazonS3 content (requires DownloadPlusAWS)
     */
    public function getLocalFileObjectsSelect($id, $name)
    {
    	$html = '';

    	if (Mage::helper('downloadplus')->existsDownloadplusFile()) {

    		if (!$block = $this->getLayout()->getBlock('downloadplus_local_file_link')) {
    			$block = $this->getLayout()->createBlock('downloadplusfile/adminhtml_local_file_link', 'downloadplus_local_file_link');
    		}
    		if ($block) {
    			$html = $block->getObjectsSelect($id, $name)->toHtml();
    		}

    	}

    	return $html;
    }

    /*
     * Return File Selection for EditionGuard content (required DownloadplusEditionguard)
     */
    public function getEditionguardObjectsSelect($id, $name)
    {
    	$html = '';

    	if (Mage::helper('downloadplus')->existsDownloadplusEditionguard()) {

    		if (!$block = $this->getLayout()->getBlock('downloadplus_editionguard_link')) {
    			$block = $this->getLayout()->createBlock('downloadpluseditionguard/adminhtml_editionguard_repository_link', 'downloadplus_editionguard_link');
    		}
    		if ($block) {
    			$html = $block->getObjectsSelect($id, $name)->toHtml();
    		}

    	}

    	return $html;
    }

    /*
     * Return Build Command Selection for Build content (required DownloadplusBuild)
     */
    public function getBuilderObjectsSelect($id, $name)
    {
    	$html = '';

    	if (Mage::helper('downloadplus')->existsDownloadplusBuilder()) {

    		if (!$block = $this->getLayout()->getBlock('downloadplus_builder_link')) {
    			$block = $this->getLayout()->createBlock('downloadplusbuilder/adminhtml_build_command', 'downloadplus_builder_link');
    		}
    		if ($block) {
    			$html = $block->getObjectsSelect($id, $name)->toHtml();
    		}

    	}

    	return $html;
    }

    /*
     * Return Build Command Selection for Build content (required DownloadplusBuild)
    */
    public function getMagazineObjectsSelect($id, $name)
    {
    	$html = '';

    	if (Mage::helper('downloadplus')->existsDownloadplusMagazine()) {

    		if (!$block = $this->getLayout()->getBlock('downloadplus_magazine_link')) {
    			$block = $this->getLayout()->createBlock('downloadplusmagazine/adminhtml_magazine_issues', 'downloadplus_magazine_link');
    		}
    		if ($block) {
    			$html = $block->getObjectsSelect($id, $name)->toHtml();
    		}

    	}

    	return $html;
    }

    /*
     * Returns the select bos for Repository Style update handling
     */
    public function getProductTypeSelect($id=null, $name=null)
    {
        $html = '';
        if (!$id) { $id = 'product_downloadable_product_type'; }
        if (!$name) { $name = 'product[downloadable_product_type]'; }

        if (Mage::helper('downloadplus')->existsDownloadplusRepository() &&  (bool)$this->getProduct()->getLinksPurchasedSeparately()==false) {
            $html = Mage::helper('downloadplus/attribute')
                        ->setProduct(Mage::registry('current_product'))
                        ->getAttributeFormHtml('catalog_product', 'downloadable_product_type', $id, $name, false);
        }

        return $html;
    }

    public function toJSHtml($html)
    {
    	// Remove line-breaks for use in JavaScript;
    	$html = str_replace(array("\r\n", "\r", "\n", "\t"), '', $html);
    	return $html;
    }

    public function getTestUrlAction()
    {
    	return Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('adminhtml/downloadplus_ajax/testUrl', array('_secure' => true));
    }

    public function getConfigJson($type='links')
    {
        if (Mage::helper('downloadplus')->isFlashUploader()) {
        	$this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()
        			->getUrl('adminhtml/downloadplus_downloadable_file/upload', array('type' => $type, '_secure' => true)));
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
        	return Mage::helper('core')->jsonEncode($this->getConfig()->getData());
        }

        return parent::getConfigJson($type);
    }

}
