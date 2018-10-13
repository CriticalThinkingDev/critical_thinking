<?php
$installer = $this;
$installer->startSetup();

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