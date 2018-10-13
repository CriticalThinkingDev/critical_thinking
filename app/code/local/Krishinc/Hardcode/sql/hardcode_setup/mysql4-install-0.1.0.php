<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('hardcode')};
CREATE TABLE {$this->getTable('hardcode')} (
  `hardcode_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `grade` varchar(255) NOT NULL default '',
  `s_id` int(11) unsigned NOT NULL,
  `g_id` int(11) unsigned NOT NULL,
  `content` text NOT NULL default '',
  `count` int(11) unsigned NOT NULL,
  `sstatus` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`hardcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 