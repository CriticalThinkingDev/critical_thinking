<?php
$installer = $this;
$installer->startSetup();

// Introduced with 0.3.45
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_event_queue')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(255) DEFAULT NULL,
  `related_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Event Queue for CRON related jobs of DownloadPlus';		
");

$installer->getConnection()->addColumn($this->getTable('downloadplus/link_purchased_item_extension'), 'attributes', "text AFTER expires_on");

$installer->endSetup();
?>