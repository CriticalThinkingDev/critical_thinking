<?php
$installer = $this;
$installer->startSetup();

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