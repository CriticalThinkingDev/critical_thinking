<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('teachingsupport')};
CREATE TABLE {$this->getTable('teachingsupport')} (
  `teachingsupport_id` int(11) unsigned NOT NULL auto_increment,
  `sku` varchar(50) NOT NULL default '',
  `pdf` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`teachingsupport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 