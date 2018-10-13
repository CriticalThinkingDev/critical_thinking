<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('standards')};
CREATE TABLE {$this->getTable('standards')} (
  `standards_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` varchar(255) NOT NULL default '',
  `sku` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `standard` text NOT NULL default '',
  `benchmark` text NOT NULL default '',
  `page_numbers` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`standards_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 