<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Downloadplus Feed Model
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    protected $_channels = Array(
    							'product'=>'https://technology.pillwax.com/software/downloadable/rss/updates/product/oss-downp-cu',
    							'magento'=>'https://technology.pillwax.com/software/downloadable/rss/updates/category/magento-extensions',
    							'news'=>'https://technology.pillwax.com/software/news/rss/tag/magento'
    						);

    const XML_UPDATE_FREQUENCY = 1;  // Update only once all 24h
    const XML_CACHE_PATH = 'downloadplus_feed_lastcheck';

    const SEVERITY_CRITICAL = 1;
    const SEVERITY_MAJOR    = 2;
    const SEVERITY_MINOR    = 3;
    const SEVERITY_NOTICE   = 4;


    protected function _construct()
    {
    }

    /**
     * Retrieve feed url for channel
     */
    public function getFeedChannelUrl($channel)
    {
    	$result = isset($this->_channels[$channel])?$this->_channels[$channel]:null;
        return $result;
    }

    /**
     * Check feed for modification
     */
    public function checkUpdate()
    {
    	$channels = explode(',', Mage::getModel('downloadplus/config')->getOptionProductRssFeed());
    	if (empty($channels)) {
    		return $this;
    	}
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }

        foreach ($channels as $channel) {
        	if ($url = $this->getFeedChannelUrl($channel)) {
		        $feedData = array();
		        $feedXml = $this->getFeedChannelData($url);

		        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
		            foreach ($feedXml->channel->item as $item) {
		                $feedData[] = array(
		                    'severity'      => self::SEVERITY_NOTICE,
		                    'date_added'    => $this->getDate((string)$item->pubDate),
		                    'title'         => (string)$item->title,
		                    'description'   => (string)$item->description,
		                    'url'           => (string)$item->link,
		                );
		            }
		            if ($feedData) {
		                Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
		            }
		        }
        	}
        }

        $this->setLastUpdate();

        return $this;
    }

    /**
     * Retrieve Update Frequency
     */
    public function getFrequency()
    {
        return self::XML_UPDATE_FREQUENCY * 3600;
    }

    /**
     * Get Last update time
     */
    public function getLastUpdate()
    {
        return Mage::app()->loadCache(self::XML_CACHE_PATH);
    }

    /**
     * Set last update time (now)
     */
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), self::XML_CACHE_PATH);
        return $this;
    }

    /**
     * Retrieve feed data as XML element
     */
    public function getFeedChannelData($url)
    {
        try {
	        $curl = new Varien_Http_Adapter_Curl();
	        $curl->setConfig(array(
	            'timeout'   => 2
	        ));
	        $curl->write(Zend_Http_Client::GET, $url, '1.0');
	        $data = $curl->read();
	        if ($data === false) {
	            return false;
	        }
	        $data = preg_split('/^\r?$/m', $data, 2);
	        $data = trim($data[1]);
	        $curl->close();

            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            return false;
        }

        return $xml;
    }

}