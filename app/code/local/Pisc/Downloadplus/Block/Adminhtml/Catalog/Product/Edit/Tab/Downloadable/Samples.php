<?php
/**
 * Downloadplus Catalog Product Edit Tab Downloadable Samples Block
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusMagazine
 * @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.4
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples extends Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadplus/product/edit/downloadable/samples.phtml');
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

    	$this->setChild(
    			'upload_button',
    			$this->getLayout()->createBlock('adminhtml/widget_button')
    			->addData(array(
    					'id'      => '',
    					'label'   => Mage::helper('adminhtml')->__('Upload Files'),
    					'type'    => 'button',
    					'onclick' => 'Downloadable.massUploadByType(\'samples\');Downloadable.massUploadByType(\'samplesimage\');'
    			))
    	);
    }

    private function _getSampleData()
    {
    	$samplesArr = array();
    	$samples = $this->getProduct()->getTypeInstance(true)->getSamples($this->getProduct());
    	foreach ($samples as $item) {
    		$tmpSampleItem = array(
    				'sample_id' => $item->getId(),
    				'title' => $item->getTitle(),
    				'sample_url' => $item->getSampleUrl(),
    				'sample_type' => $item->getSampleType(),
    				'sort_order' => $item->getSortOrder(),
    		);

    		$file = Mage::helper('downloadable/file')->getFilePath(
    			Pisc_Downloadplus_Model_Sample::getBasePath($item->getSampleType()), $item->getSampleFile()
    		);
    		if ($item->getSampleFile() && is_file($file)) {
    			$tmpSampleItem['file_save'] = array(
    					array(
    						'file' => $item->getSampleFile(),
    						'name' => Mage::helper('downloadable/file')->getFileFromPathFile($item->getSampleFile()),
    						'size' => filesize($file),
    						'status' => 'old'
    					));
    		}

    		$imageFile = Mage::helper('downloadable/file')->getFilePath(
    			Pisc_Downloadplus_Model_Sample_Image::getBasePath(), $item->getImageFile()
    		);
    		if ($item->getImageFile() && is_file($imageFile)) {
    			$tmpSampleItem['image_file_save'] = array(
    					array(
    						'file' => $item->getImageFile(),
    						'name' => Mage::helper('downloadable/file')->getFileFromPathFile($item->getImageFile()),
    						'size' => filesize($imageFile),
    						'status' => 'old'
    					));
    			$tmpSampleItem['image_preview'] = '<img src="'.$item->getImageUrl('thumbnail').'" />';
    		}

    		if ($this->getProduct() && $item->getStoreTitle()) {
    			$tmpSampleItem['store_title'] = $item->getStoreTitle();
    		}
    		$samplesArr[] = new Varien_Object($tmpSampleItem);
    	}

    	return $samplesArr;
    }

    /*
     * Return link data
    */
    public function getSampleData()
    {
    	Mage::helper('downloadplus/download');

    	$data = $this->_getSampleData();

    	foreach ($data as $item) {
    		$item->setData('amazon_s3_object', null);
    		$item->setData('amazon_cf_object', null);
    		$item->setData('file_local_object', null);

    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3) {
    			$item->setData('amazon_s3_object', $item->getData('sample_url'));
    			$item->unsetData('sample_url');
    		}
    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF) {
    			$item->setData('amazon_cf_object', $item->getData('sample_url'));
    			$item->unsetData('sample_url');
    		}
    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL) {
    			if ($file = $item->getData('file_save')) {
    				$item->setData('file_local_object', $file[0]['file']);
    				$item->unsetData('file_save');
    			}
    		}
    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD) {
    			$item->setData('editionguard_object', $item->getData('sample_url'));
    			$item->unsetData('sample_url');
    		}
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
	    		if (!$block = $this->getLayout()->getBlock('downloadplus_amazon_s3_sample')) {
	    			$block = $this->getLayout()->createBlock('downloadplusaws/adminhtml_amazon_s3_sample', 'downloadplus_amazon_s3_sample');
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

	    	$config = Mage::getModel('downloadplusaws/config')->setStore();

	    	if ($config->isAdminLinkStyleBuckets()) {
    			if (!$block = $this->getLayout()->getBlock('downloadplus_amazon_s3_sample')) {
    				$block = $this->getLayout()->createBlock('downloadplusaws/adminhtml_amazon_s3_sample', 'downloadplus_amazon_s3_sample');
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

    public function getConfigJson($type='samples')
    {
        if (Mage::helper('downloadplus')->isFlashUploader()) {
        	$this->getConfig()->setUrl(Mage::getModel('adminhtml/url')
        			->addSessionParam()
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

    public function getBrowseButtonHtml($type = '')
    {
        return $this->getChild('browse_button')
                // Workaround for IE9
                ->setBeforeHtml('<div style="display:inline-block; " id="downloadable_sample_{{id}}_' . $type . 'file-browse">')
                ->setAfterHtml('</div>')
                ->setId('downloadable_sample_{{id}}_' . $type . 'file-browse')
                ->toHtml();
    }

    public function getDeleteButtonHtml($type = '')
    {
        return $this->getChild('delete_button')
                ->setLabel('')
                ->setId('downloadable_sample_{{id}}_' . $type . 'file-delete')
                ->setStyle('display:none; width:31px;')
                ->toHtml();
    }

}
