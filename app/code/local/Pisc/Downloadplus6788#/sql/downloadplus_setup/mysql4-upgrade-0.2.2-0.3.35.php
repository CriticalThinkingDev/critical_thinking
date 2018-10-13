<?php
$installer = $this;
$installer->startSetup();

// Introduced with 0.3.0
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_download_detail')} (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `link_id` int(11) DEFAULT NULL,
  `link_sample_id` int(11) DEFAULT NULL,
  `sample_id` int(11) DEFAULT NULL,
  `file` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `version` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Downloadplus Download Details Table';
");

// Introduced with 0.3.6
$installer->run("
ALTER TABLE {$this->getTable('downloadplus_download_detail')}
	ADD `link_customer_item_id` int(11) NULL AFTER `sample_id`
;
ALTER TABLE {$this->getTable('downloadplus_download_detail')}
	ALTER `link_customer_item_id` SET DEFAULT NULL
;
");

$installer->run("
ALTER TABLE {$this->getTable('downloadplus_download_detail')}
	ADD `link_product_item_id` int(11) NULL AFTER `link_customer_item_id`
;
ALTER TABLE {$this->getTable('downloadplus_download_detail')}
	ALTER `link_product_item_id` SET DEFAULT NULL
;
");

// Introduced with 0.3.6
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_link_customer_item')} (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchased_id` int(10) unsigned DEFAULT NULL,
  `order_item_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `link_hash` varchar(255) NOT NULL DEFAULT '',
  `number_of_downloads_bought` int(10) unsigned NOT NULL DEFAULT '0',
  `number_of_downloads_used` int(10) unsigned NOT NULL DEFAULT '0',
  `link_id` int(20) unsigned NOT NULL DEFAULT '0',
  `link_title` varchar(255) NOT NULL DEFAULT '',
  `is_shareable` smallint(1) unsigned NOT NULL DEFAULT '0',
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_file` varchar(255) NOT NULL DEFAULT '',
  `link_type` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(50) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`item_id`),
  KEY `DOWNLOADPLUS_CUSTOMER_LINK_PURCHASED_ID` (`purchased_id`),
  KEY `DOWNLOADPLUS_CUSTOMER_ORDER_ITEM_ID` (`order_item_id`),
  KEY `DOWNLOADPLUS_CUSTOMER_LINK_HASH` (`link_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Downloadplus additional Customer Download Items';
");

$installer->run("
ALTER TABLE {$this->getTable('downloadplus_link_customer_item')}
  ADD CONSTRAINT `FK_DOWNLOADPLUS_CUSTOMER_LINK_PURCHASED_ID` FOREIGN KEY (`purchased_id`) REFERENCES {$this->getTable('downloadable_link_purchased')} (`purchased_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DOWNLOADPLUS_CUSTOMER_ORDER_ITEM_ID` FOREIGN KEY (`order_item_id`) REFERENCES {$this->getTable('sales_flat_order_item')} (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_link_product_item')} (
  link_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  product_id int(10) unsigned NOT NULL DEFAULT '0',
  link_title varchar(255) NOT NULL DEFAULT '',
  link_url varchar(255) NOT NULL DEFAULT '',
  link_file varchar(255) NOT NULL DEFAULT '',
  link_type varchar(20) NOT NULL DEFAULT '',
  sort_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (link_id),
  KEY DOWNLODABLE_SAMPLE_PRODUCT (product_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Downloadplus additional Product Download Items';
");

$installer->run("
ALTER TABLE {$this->getTable('downloadplus_link_product_item')}
  ADD CONSTRAINT FK_DOWNLODPLUS_PRODUCT_LINK FOREIGN KEY (product_id) REFERENCES {$this->getTable('catalog_product_entity')} (entity_id) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_link_purchased_item_serialnumber')} (
  `serial_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchased_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned DEFAULT '0',
  `link_id` int(20) unsigned NOT NULL DEFAULT '0',
  `serial_title` varchar(255) NOT NULL DEFAULT '',
  `serial_number` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(50) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`serial_id`),
  KEY `DOWNLOADPLUS_CUSTOMER_LINK_PURCHASED_ID` (`purchased_id`),
  KEY `DOWNLOADPLUS_CUSTOMER_ORDER_ITEM_ID` (`order_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Downloadplus Serial Number for Downloadable Purchased Items';
");

$dir = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'customer';
if (!file_exists($dir)) {
	@mkdir($dir, 0770, true);
}
$dir = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'customer' . DS . 'links';
if (!file_exists($dir)) {
	@mkdir($dir, 0770, true);
}
$dir = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'product';
if (!file_exists($dir)) {
	@mkdir($dir, 0770, true);
}
$dir = Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'product' . DS . 'links';
if (!file_exists($dir)) {
	@mkdir($dir, 0770, true);
}

// Changes Introduced with 0.3.11
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'serial_number')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
		CHANGE COLUMN serial_number serial_number TEXT
	;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'serial_hash')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
		ADD `serial_hash` varchar(255) NULL AFTER `serial_number`
	;
	");
}

// Changes Introduced with 0.3.13
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_customer_item'), 'purchased_id')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_customer_item')}
		CHANGE COLUMN purchased_id purchased_id INT(10) unsigned DEFAULT NULL
	;
	");
}
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_customer_item'), 'order_item_id')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_customer_item')}
		CHANGE COLUMN order_item_id order_item_id INT(10) unsigned DEFAULT NULL
	;
	");
}
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_customer_item'), 'product_id')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_customer_item')}
		CHANGE COLUMN product_id product_id INT(10) unsigned DEFAULT NULL
	;
	");
}

// Changes Introduced with 0.3.14
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'purchased_id')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
		CHANGE COLUMN purchased_id purchased_id INT(10) unsigned DEFAULT NULL
	;
	");
}
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'order_item_id')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
		CHANGE COLUMN order_item_id order_item_id INT(10) unsigned DEFAULT NULL
	;
	");
}
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'product_id')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
		CHANGE COLUMN product_id product_id INT(10) unsigned DEFAULT NULL
	;
	");
}
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'link_id')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
		CHANGE COLUMN link_id link_id INT(20) unsigned DEFAULT NULL
	;
	");
}

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_product_serialnumber')} (
  `serial_hash` varchar(255) NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `serial_number` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`serial_hash`),
  KEY `DOWNLOADPLUS_PRODUCT_SERIALNUMBER_PRODUCT_ID` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='DownloadPlus pool of Serialnumbers for Products';
");

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

// Introduced with 0.3.35
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_download_detail'), 'store_id')) {
	$installer->run("
			ALTER TABLE {$this->getTable('downloadplus_download_detail')}
					MODIFY COLUMN `store_id` smallint(6) NOT NULL DEFAULT '0'
;
");
}

$installer->run("
		CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_link_product_item_title')} (
				`title_id` int(11) NOT NULL AUTO_INCREMENT,
				`link_id` int(11) NOT NULL DEFAULT '0',
				`store_id` smallint(6) NOT NULL DEFAULT '0',
				`title` varchar(255) NOT NULL DEFAULT '',
				PRIMARY KEY (`title_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='DownloadPlus Additional Product Downloads Title';
	");

if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_product_item'), 'link_title')) {
	// Transfer Link Titles
	$collection = Mage::getModel('downloadplus/link_product_item')->getCollection();
	foreach ($collection as $item) {
		$title = $item->setStoreId(0)->getLinkTitle();
		if (!$title) {
			Mage::getModel('downloadplus/link_product_item_title')
			->getResource()
			->saveTitle($item);
		}
	}
	$installer->run("ALTER TABLE {$this->getTable('downloadplus_link_product_item')} DROP link_title;");
}

$installer->endSetup();
?>