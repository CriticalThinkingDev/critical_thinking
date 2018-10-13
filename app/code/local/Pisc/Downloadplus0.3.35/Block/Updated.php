<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product and Samples Updated block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_Updated extends Mage_Core_Block_Template
{
	const CACHE_TAG = 'downloadplus_block_updated';

	public function __construct() {
		parent::__construct();
		
		$this->addData(array(
            'cache_lifetime'	=> 3600,
            'cache_tags'        => array(self::CACHE_TAG),
            //'cache_key'       => null,
        ));		
	}

	protected function _toHtml() {
		return parent::_toHtml();
	}

	/*
	 * Returns Array of Downloads with Title and associated data
	 */
	public function getUpdated($limit = 0)
	{
		$result = Mage::getSingleton('downloadplus/downloads')
					->setStoreId(Mage::app()->getStore()->getId())
					->getUpdates();
		if ($limit>0) {
			$result = array_slice($result, 0, $limit);
		}
		return $result;
	}

	/*
	 * Returns Array of Download Links with Title and associated data
	 */
	public function getUpdatedLinks($limit = 0)
	{
		$result = Mage::getSingleton('downloadplus/downloads')
					->setStoreId(Mage::app()->getStore()->getId())
					->getLinkUpdates();
		if ($limit>0) {
			$result = array_slice($result, 0, $limit);
		}
		return $result;
	}

	/*
	 * Returns Array of Download Sample with Title and associated data
	 */
	public function getUpdatedSamples($limit = 0)
	{
		$result = Mage::getSingleton('downloadplus/downloads')
					->setStoreId(Mage::app()->getStore()->getId())
					->getSampleUpdates();
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
		$params = Array();
		$result = $this->getUrl('downloadable/rss/updates', $params);
		return $result;
	}

}
