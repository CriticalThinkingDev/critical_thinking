<?php
/**
 * Unirgy_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Unirgy
 * @package    Unirgy_StoreLocator
 * @copyright  Copyright (c) 2008 Unirgy LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Unirgy
 * @package    Unirgy_StoreLocator
 * @author     Boris (Moshe) Gurevich <moshe@unirgy.com>
 */
class Unirgy_StoreLocator_Helper_Data extends Mage_Core_Helper_Data
{
    protected $_locations = array();

    public function getLocation($id)
    {
        if (!isset($this->_locations[$id])) {
            $location = Mage::getModel('ustorelocator/location')->load($id);
            $this->_locations[$id] = $location->getId() ? $location : false;
        }
        return $this->_locations[$id];
    }

    public function populateEmptyGeoLocations()
    {
        set_time_limit(0);
        ob_implicit_flush();
        $collection = Mage::getModel('ustorelocator/location')->getCollection();
        $collection->getSelect()->where('latitude=0');
        foreach ($collection as $loc) {
//            echo $loc->getTitle()."<br/>";
            $loc->save();
        }
    }

    public function getDefaultLocations()
    {
        $country = Mage::getStoreConfig('ustorelocator/general/default_country');
        /* @var $collection Unirgy_StoreLocator_Model_Mysql4_Location_Collection */
        $collection = Mage::getModel('ustorelocator/location')->getCollection();
        $store = $this->getStoreId();
        // filter by store, show only current store locations, or those without any location
        $collection->addStoreFilter($store)
            ->addFieldToFilter('latitude', array('neq' => 0))
            ->addOrder('is_featured', Zend_Db_Select::SQL_DESC)
            ->addOrder('title', Zend_Db_Select::SQL_ASC);

        if($country) {
            $collection->addFieldToFilter('country', $country);
        }
        $data = $collection->getData();
        foreach($data as $i => &$item) {
            $item['units'] = null;
            $item['distance'] = null;
            if(!empty($item['use_label'])) {
                $item['marker_label'] = ++$i;
            }
            if(!empty($item['icon'])) {
                $v = ltrim($item['icon'], '/');
                if($icon_info = @getimagesize(Mage::getBaseDir('media') . DS . $v)) {
                    $item['icon_width'] =  $icon_info[0];
                    $item['icon_height'] = $icon_info[1];
                }
                $item['icon'] = Mage::getBaseUrl('media') . $v;
            }

            if(!empty($item['is_featured'])) {
                $item['is_featured'] = (boolean) $item['is_featured'];
            }
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getIconsDir()
    {
        return Mage::getBaseDir('media') . $this->getIconDirPrefix();
    }

    /**
     * @return string
     */
    public function getIconDirPrefix()
    {
        return DS . 'storelocator' . DS . 'locations' . DS . 'icons';
    }
}
