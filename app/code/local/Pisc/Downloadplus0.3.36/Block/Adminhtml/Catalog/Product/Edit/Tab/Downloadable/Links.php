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
 * @version		0.1.7
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

    public function hasLinkAttributes()
    {
    	$helper = Mage::helper('downloadplus');
    	return $helper->hasCustomDownloadableAttributes($this->getProduct(), 'link');
    }

    /*
     * Return link data
     */
    public function getLinkData()
    {
    	Mage::helper('downloadplus/download');
    	 
    	$data = parent::getLinkData();
    	
    	foreach ($data as $item) {
    		$extension = Mage::getModel('downloadplus/link_extension')->loadByLinkId($item->getLinkId());
    		$item->setAttributes($extension->getAttributes());
    		
    		$item->setData('amazon_s3_object', null);
    		$item->setData('amazon_s3_bucket', null);
    		$item->setData('amazon_s3_file', null);
    		$item->setData('amazon_cf_object', null);

    		$item->setData('sample_amazon_s3_object', null);
    		$item->setData('sample_amazon_s3_bucket', null);
    		$item->setData('sample_amazon_s3_file', null);
    		$item->setData('sample_amazon_cf_object', null);
    		
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
