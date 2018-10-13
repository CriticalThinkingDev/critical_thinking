<?php
$installer = $this;
$installer->startSetup();

// Introduced with 0.3.17
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_link_extension')} (
  id int(11) NOT NULL AUTO_INCREMENT,
  link_id int(11) NOT NULL,
  expiry tinyint(4) DEFAULT NULL,
  expire_on varchar(50) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY link_id (link_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Data extension for downloadable links';
");

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_link_purchased_item_extension')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `expires_on` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Data extension for purchased downloadable items';
");

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