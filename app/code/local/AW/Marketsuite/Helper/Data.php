<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    1.2.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Marketsuite_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SESSION_BACKURL_KEY = '_aw_back_url';
    const USE_AW_BACKURL_FLAG = '_use_aw_backurl';

    private $_countries;
    private $_options;
    private $extensionEnabled = array();

    public function CheckUselessProductAttributes($code)
    {
        $disabledAttributes = array(
            'Activation Information', 'Date from', 'Date to', 'Custom Layout Update',
            'Description', 'In Depth', 'Display product options in', 'Exclude days',
            'Multiply options', 'Price rules', 'Billable period', 'Period type',
            'Contrast Ratio', 'Custom Design', 'Meta Description', 'Meta Title',
            'Meta Keywords', 'Page Layout', 'Set Product as New from Date', 'Set Product as New to Date',
            'shape', 'URL key',
        );
        if (in_array($code, $disabledAttributes))
            return true;
        return false;
    }

    public function extensionEnabled($extension_name)
    {
        if (!isset($this->extensionEnabled[$extension_name])) {
            $modules = (array)Mage::getConfig()->getNode('modules')->children();
            if (!isset($modules[$extension_name])
                || $modules[$extension_name]->descend('active')->asArray() == 'false'
                || Mage::getStoreConfig('advanced/modules_disable_output/' . $extension_name)
            )
                $this->extensionEnabled[$extension_name] = false;
            else
                $this->extensionEnabled[$extension_name] = true;
        }
        return $this->extensionEnabled[$extension_name];
    }

    public function getRegions()
    {
        if (!$this->_options) {
            $countriesArray = Mage::getResourceModel('directory/country_collection')->load()
                ->toOptionArray(false);
            $this->_countries = array();
            foreach ($countriesArray as $a) {
                $this->_countries[$a['value']] = $a['label'];
            }

            $countryRegions = array();
            $regionsCollection = Mage::getResourceModel('directory/region_collection')->load();
            foreach ($regionsCollection as $region) {
                $countryRegions[$region->getCountryId()][$region->getId()] = $region->getDefaultName();
            }
            uksort($countryRegions, array($this, 'sortRegionCountries'));

            $this->_options = array();
            foreach ($countryRegions as $countryId => $regions) {
                $regionOptions = array();
                foreach ($regions as $regionName) {
                    $regionOptions[] = array('label' => $regionName, 'value' => $regionName);
                }
                $this->_options[] = array('label' => $this->_countries[$countryId], 'value' => $regionOptions);
            }
        }
        $options = $this->_options;
        array_unshift($options, array('value' => '', 'label' => ''));

        return $options;
    }

    public function sortRegionCountries($a, $b)
    {
        return strcmp($this->_countries[$a], $this->_countries[$b]);
    }

    public function getCategoriesArray()
    {
        $categories = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('path', 'asc');
        foreach ($categories as $category) {
            if (!trim($category->getName()) || $category->getLevel() == 1 || $category->getLevel() == 0) {
                continue;
            }
            $multiOptions[] = array('value' => $category->getEntityId(), 'label' => $category->getName());
        }

        return $multiOptions;
    }

    public function getStoresArray()
    {
        $multiOptions = array();
        $stores = Mage::getModel('core/store')->getCollection();

        $multiOptions[] = array('value' => 0, 'label' => Mage::helper('marketsuite')->__('Admin store'));

        foreach ($stores as $store) {
            if (!trim($store->getName())) {
                continue;
            }
            $multiOptions[] = array('value' => $store->getId(), 'label' => Mage::helper('marketsuite')->__('%s store of %s website', $store->getName(), $store->getWebsite()->getName()));
        }

        return $multiOptions;

    }
    
    public function getStatusesArray()
    {
        $statusOptions = array();
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        foreach ($statuses as $key => $status) {
            $statusOptions[] = array('value' => $key, 'label' => $status);
        }
        return $statusOptions;
    }
    
    /*
     * Returns Advanced Newsletter extension version
     */

    public static function getAdvancedNewsletterVersion()
    {
        return Mage::getConfig()->getModuleConfig('AW_Advancednewsletter')->version;
    }

    protected function _getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    public function setBackUrl($url)
    {
        $this->_getAdminhtmlSession()->setData(self::SESSION_BACKURL_KEY, $url);
    }

    public function getBackUrl()
    {
        return $this->_getAdminhtmlSession()->getData(self::SESSION_BACKURL_KEY);
    }
}
