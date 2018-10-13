<?php
/**
 * RSS Feed block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.1
 */

class Pisc_Downloadplus_Block_Rss_Feed extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
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
		$result = $this->getUrl('downloadable/rss/updates', $params);
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
		$result = $this->getUrl('downloadable/rss/updates', $params);
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
		$result = $this->getUrl('downloadable/rss/updates', $params);
		return $result;
	}

}
