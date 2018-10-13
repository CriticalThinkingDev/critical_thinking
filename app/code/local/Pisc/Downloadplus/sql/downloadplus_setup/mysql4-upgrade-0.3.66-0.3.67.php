<?php
$installer = $this;
$installer->startSetup();

// Check for Table Field
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_history'), 'bonus_link_id')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_link_history')}
		ADD `bonus_link_id` int(10) unsigned DEFAULT NULL AFTER `link_id`
		;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_history'), 'product_id')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_link_history')}
		ADD `product_id` int(10) unsigned AFTER `bonus_link_id`
		;
	");
}

// Update Pool Names on Global Serialnumber Pools
Mage::getModel('downloadplus/config');

$changedPools = Array();
$collection = Mage::getModel('downloadplus/product_serialnumber')->getCollection();

$collection->setPageSize(100);
$pages = $collection->getLastPageNumber();
$currentPage = 1;
do {
    $collection->setCurPage($currentPage);
    $collection->load();
        
    foreach ($collection as $item) {
        if (!is_null($item->getData('product_id')) && is_null($item->getData('serial_number_pool'))) {
            $item->setData('serial_number_pool', Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT);
            $item->save();
        }
    
        $pool = $item->getSerialNumberPool();
        if (is_null($item->getData('product_id')) && $pool!==false && strpos($pool, Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL)!==0) {
            $changedPools[str_replace(' ', '_', $pool)] = Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL.$pool;
            $item->setData('serial_number_pool', Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL.$pool);
            $item->save();
        }
    }
    
    $currentPage++;
    $collection->clear();    
} while ($currentPage <= $pages);
    
$collection = Mage::getModel('downloadplus/link_extension')->getCollection();

$collection->setPageSize(100);
$pages = $collection->getLastPageNumber();
$currentPage = 1;
do {

    foreach ($collection as $item) {
        if (is_null($item->getData('serial_number_pool'))) {
            $item->setData('serial_number_pool', Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE);
            $item->save();
        } elseif (!empty($changedPools)) {
            foreach ($changedPools as $oldPool=>$newPool) {
                $pool = str_replace(' ', '_', $item->getData('serial_number_pool'));
                if (isset($changedPools[$pool])) {
                    $item->setData('serial_number_pool', Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL.$item->getData('serial_number_pool'));
                    $item->save();
                }
            }
        }
        if (is_null($item->getData('serial_number_pool_unlock'))) {
            $item->setData('serial_number_pool_unlock', Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE);
            $item->save();
        }
    }

    $currentPage++;
    $collection->clear();
} while ($currentPage <= $pages);

$installer->endSetup();
?>