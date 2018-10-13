<?php
$installer = $this;
$installer->startSetup();

// Introduced with 0.3.23 - Global Serialnumbers
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_product_serialnumber'), 'product_id')) {
$installer->run("
ALTER TABLE {$this->getTable('downloadplus_product_serialnumber')}
	MODIFY `product_id` int(10) unsigned DEFAULT NULL
;
");
}

$helper = Mage::helper('downloadplus');
$extensions = Mage::getModel('downloadplus/link_extension')->getCollection();
foreach ($extensions as $extension) {
	$pool = $extension->getData('serial_number_pool');
	if (empty($pool)) {
		if ($helper->hasSerialnumbers(null, $extension->getLinkId())) {
			$extension->setData('serial_number_pool', Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT);
		} else {
			$extension->setData('serial_number_pool', Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE);
		}
		$extension->save();
	}
}

// Introduced with 0.3.32
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_log'), 'link_id')) {
	$installer->run("
			ALTER TABLE {$this->getTable('downloadplus_log')}
					ADD `link_id` int(11) DEFAULT NULL AFTER `item_id`
;
");
}

$installer->endSetup();
?>