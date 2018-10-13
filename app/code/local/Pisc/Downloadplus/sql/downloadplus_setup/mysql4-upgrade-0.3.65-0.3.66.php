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

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_link_title_history')} (
    `title_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Title ID',
    `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Link ID',
    `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
    `title` varchar(255) DEFAULT NULL COMMENT 'Title',
    `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='DownloadPlus Link Title History';
");

$installer->endSetup();
?>