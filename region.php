<?php

require_once('app/Mage.php'); //Path to Magento
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);

Mage::app();

$regionCollection = Mage::getResourceModel('directory/region_collection')->addFieldToFilter('name','Armed Forces Africa')->getFirstItem();
$regionCollection->setCode('AF')->save();
$regionCollection = Mage::getResourceModel('directory/region_collection')->addFieldToFilter('name','Armed Forces Middle East')->getFirstItem();
$regionCollection->setCode('AM')->save();
$regionCollection = Mage::getResourceModel('directory/region_collection')->addFieldToFilter('name','Armed Forces Canada')->getFirstItem();
$regionCollection->setCode('AC')->save();

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');
//$query = 'SELECT * FROM ' . $resource->getTableName('premiumrate_shipping/premiumrate');
$regionCollection = Mage::getResourceModel('directory/region_collection')->addCountryFilter(array('US'))->load();

/**
 * Execute the query and store the results in $results
 */
//$results = $readConnection->fetchAll($query);
$results = $regionCollection->getData();

echo '<pre>'; print_r($results); echo '</pre>';

