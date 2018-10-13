<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Additional Downloads Feed Block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.0
 */

class Pisc_Downloadplus_Block_Rss_Additional extends Mage_Rss_Block_Abstract
{

	protected $_product = null;
	protected $_category = null;
	protected $_store_id = null;

    protected function _construct()
    {
    	$params = Mage::registry('downloadplus_rss_feed');
    	if ($params) {
    		if ($param = $params->getData('product_sku')) {
	    		$product = Mage::getModel('catalog/product');
	    		if ($id = $product->getIdBySku(strtoupper($param))) {
	    			$product->load($id);
	    			if ($product->getId()) {
	    				$this->_product = $product;
	    			}
	    		}
    		}
    		if ($param = $params->getData('category_id')) {
    			$this->_category = Mage::getModel('catalog/category')->load($param);
    		}
    		if ($param = $params->getData('category_url_key')) {
    			$this->_category = Mage::getModel('catalog/category')->loadByAttribute('url_key', $param);
    		}
    		if ($param = $params->getData('store_id')) {
    			$this->_store_id = $param;
    		}
    	}

        /*
         * Setting cache to save the rss for 10 minutes
         */
    	$cacheKey = 'downloadplus_rss_additional';
    	if ($this->_store_id) {
    		$cacheKey.='_store_'.$this->_store_id;
    	}
    	if ($this->_product) {
    		$cacheKey.='_product_'.$this->_product->getId();
    	}
    	if ($this->_category) {
    		$cacheKey.='_category_'.$this->_category->getId();
    	}
        $this->setCacheKey($cacheKey);
        $this->setCacheLifetime(600);
    }

    protected function formatTimestamp($date=null)
    {
    	if ($date) {
    		$zendDate = new Zend_Date($date);
    		return $zendDate->toString('EEE, dd MMM YYYY HH:mm:ss ZZZZ');
    	}
    	return null;
    }

    protected function _toHtml()
    {
        $storeId = $this->_getStoreId();
        $rssObj = Mage::getModel('rss/rss');

        $title = $this->getChildHtml('downloadplus.rss.additional.header.title', false);
        $description = $this->getChildHtml('downloadplus.rss.additional.header.description', false);
        $link = Mage::getBaseUrl();

        $data = array(
        			'title' => $title,
        			'description' => $description,
                    'link'        => $link,
                    'charset'     => 'UTF-8',
        			'author'	=> Mage::getStoreConfig('design/head/default_title'),
        			'image'		=> $this->getBaseUrl().'media/sales/store/logo_html/'.Mage::getStoreConfig('sales/identity/logo_html')
                );

        $rssObj->_addHeader($data);

        $downloads = $this->getDownloads();
        foreach ($downloads as $download) {
        	Mage::register('downloadplus_rss_additional_item', $download);

	        $title = $this->getChildHtml('downloadplus.rss.additional.feed.title', false);
	        $description = $this->getChildHtml('downloadplus.rss.additional.feed.description', false);
	        $content = $this->getChildHtml('downloadplus.rss.additional.feed.content', false);

	        $data = array(
	        			'title' => $title,
	        			'description' => $description,
	        			'content' => $content,
	                    'charset'     => 'UTF-8'
	                );

	        if ($download->getProductId()) {
	        	$product = Mage::getModel('catalog/product')->load($download->getProductId());
	        	$data['link'] = $product->getProductUrl();
	        	$data['image'] = $product->getImageUrl();
	        }

	        $rssObj->_addEntry($data);

	        Mage::unregister('downloadplus_rss_additional_item');
        }

        return $rssObj->createRssXml();
    }

	/*
	 * Returns Array of Downloads with Title and associated data
	 */
	public function getDownloads($limit = 0)
	{
		if ($this->_product) {
			$result = Mage::getModel('downloadplus/link_product_item')
						->getCollection()
						->getByProductId($this->_product->getId(), $this->_store_id);
		} else {
			$result = Mage::getModel('downloadplus/link_product_item')
						->getCollection();
		}

		if ($limit>0) {
			$result = array_slice($result, 0, $limit);
		}
		return $result;
	}

	/**
	 * Returns if the RSS Feed is available
	 */
	public function isRssAvailable()
	{
		$config = Mage::getModel('downloadplus/config');
		return $config->isDownloadableRssFeed();
	}


	/**
	 * Returns the Link to the RSS Feed of the Version History
	 */
	public function getRssLink()
	{
		$params = Array('_secure'=>true);
		$result = $this->getUrl('downloadable/rss/additional', $params);
		return $result;
	}

	/**
	 * Returns the Link to the RSS Feed of a Product
	 */
	public function getRssLinkForProduct($product)
	{
		$params = Array(
						'product'=>$product->getSku(),
						'_secure'=>true
					);
		$result = $this->getUrl('downloadable/rss/additional', $params);
		return $result;
	}

	/**
	 * Returns the Link to the RSS Feed of a Category
	 */
	public function getRssLinkForCategory($category)
	{
		$params = Array('category'=>$category->getUrlKey(),
						'_secure'=>true
					);
		$result = $this->getUrl('downloadable/rss/additional', $params);
		return $result;
	}

}