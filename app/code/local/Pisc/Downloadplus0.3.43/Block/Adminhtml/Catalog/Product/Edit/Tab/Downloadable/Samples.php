<?php
/**
 * @category   Pisc
 * @package    Pisc_DownloadPlus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Extending Core Adminhtml catalog product downloadable items tab links section
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.8
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
