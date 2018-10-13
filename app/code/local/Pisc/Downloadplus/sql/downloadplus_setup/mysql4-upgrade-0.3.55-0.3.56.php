<?php
$installer = $this;
$installer->startSetup();

// Changes Introduced with 0.3.56
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_extension'), 'image_file')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_extension')}
	  	ADD `image_file` varchar(255) DEFAULT NULL AFTER `link_id`
	;
	");
}

// Introduced with 0.3.56
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_link_history')} (
	`item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`link_id` int(10) unsigned DEFAULT NULL,
	`bonus_link_id` int(10) unsigned DEFAULT NULL,
	`link_type` varchar(20) NOT NULL,
	`link_url` varchar(255) NOT NULL,
	`link_file` varchar(255) NOT NULL,
	`updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link History of DownloadPlus for correct file retrieval on deleted links';
");

$installer->endSetup();
?>