<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('educent')};
CREATE TABLE {$this->getTable('educent')} (
  `educent_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` varchar(255) NOT NULL default '',
  `real_order_id` varchar(255) NOT NULL default '',
  `track` int(11) unsigned NOT NULL,
  `created_time` datetime NULL,
  PRIMARY KEY (`educent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 