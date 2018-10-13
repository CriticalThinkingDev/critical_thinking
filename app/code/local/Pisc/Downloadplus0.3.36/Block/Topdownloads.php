<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Product and Samples Top Download block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

class Pisc_Downloadplus_Block_Topdownloads extends Mage_Core_Block_Template
{
	const CACHE_TAG = 'downloadplus_block_topdownloads';
	
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
	 * Returns Array of Top Downloads Links and Samples
	 */
	public function getTopDownloads($limit = 0)
	{
		$result = Mage::getSingleton('downloadplus/downloads')
					->setStoreId(Mage::app()->getStore()->getId())
					->getTopDownloads();
		if ($limit>0) {
			$result = array_slice($result, 0, $limit);
		}
		return $result;
	}

	/*
	 * Returns Array of Top Downloads Links only
	 */
	public function getTopLinks($limit = 0)
	{
		$result = Mage::getSingleton('downloadplus/downloads')
					->setStoreId(Mage::app()->getStore()->getId())
					->getTopLinks();
		if ($limit>0) {
			$result = array_slice($result, 0, $limit);
		}
		return $result;
	}

	/*
	 * Returns Array of Top Downloads Samples only
	 */
	public function getTopSamples($limit = 0)
	{
		$result = Mage::getSingleton('downloadplus/downloads')
					->setStoreId(Mage::app()->getStore()->getId())
					->getTopSamples();
		if ($limit>0) {
			$result = array_slice($result, 0, $limit);
		}
		return $result;
	}

}
