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
 * @version		0.1.3
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

    /*
     * Return link data
    */
    public function getSampleData()
    {
    	Mage::helper('downloadplus/download');
    
    	$data = parent::getSampleData();

    	foreach ($data as $item) {
    		$item->setData('amazon_s3_object', null);
    		$item->setData('amazon_cf_object', null);
    		
    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3) {
    			$item->setData('amazon_s3_object', $item->getData('sample_url'));
    			$item->unsetData('sample_url');
    		}
    		if ($item->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF) {
    			$item->setData('amazon_cf_object', $item->getData('sample_url'));
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
