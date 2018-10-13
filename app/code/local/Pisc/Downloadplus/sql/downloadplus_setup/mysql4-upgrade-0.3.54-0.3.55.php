<?php
$installer = $this;
$installer->startSetup();

// Changes Introduced with 0.3.55
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_download_detail'), 'link_bonus_item_id')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_download_detail')}
	  	ADD `link_bonus_item_id` int(11) DEFAULT NULL AFTER `link_product_item_id`
	;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_product_item'), 'attributes')) {
	$installer->run("
	ALTER TABLE {$this->getTable('downloadplus_link_product_item')}
		ADD `attributes` text AFTER `sort_order`
	;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_event_queue'), 'attributes')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_event_queue')}
		ADD `attributes` text AFTER `status`
	;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_log'), 'bonus_item_id')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_log')}
		ADD `bonus_item_id` int(11) DEFAULT NULL AFTER `customer_item_id`
	;
	");
}

$installer->endSetup();
?>