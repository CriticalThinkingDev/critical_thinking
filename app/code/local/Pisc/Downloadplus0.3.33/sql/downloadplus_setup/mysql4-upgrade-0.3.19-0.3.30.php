<?php
$installer = $this;
$installer->startSetup();

// Introduced with 0.3.20
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_download_detail'), 'store_id')) {
$installer->run("
ALTER TABLE {$this->getTable('downloadplus_download_detail')}
	ADD `store_id` smallint(6) DEFAULT NULL AFTER `detail_id`
;
");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_product_serialnumber'), 'serial_number_pool')) {
	$installer->run("
ALTER TABLE {$this->getTable('downloadplus_product_serialnumber')}
  	ADD `serial_number_pool` varchar(255) DEFAULT NULL AFTER `serial_number`
;
");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_extension'), 'serial_number_pool')) {
	$installer->run("
ALTER TABLE {$this->getTable('downloadplus_link_extension')}
  	ADD `serial_number_pool` varchar(255) DEFAULT NULL AFTER `expire_on`
;
");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_extension'), 'attributes')) {
	$installer->run("
ALTER TABLE {$this->getTable('downloadplus_link_extension')}
  	ADD `attributes` text AFTER `serial_number_pool`
;
");
}

// Introduced with 0.3.23 - Global Serialnumbers
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_product_serialnumber'), 'product_id')) {
$installer->run("
ALTER TABLE {$this->getTable('downloadplus_product_serialnumber')}
	MODIFY `product_id` int(10) unsigned DEFAULT NULL
;
");
}

$installer->endSetup();
?>