<?php

$installer = $this;

$installer->startSetup();

$installer->run("	
	-- DROP TABLE IF EXISTS {$this->getTable('customcontact')};
	CREATE TABLE {$this->getTable('customcontact')} (
	  `customcontact_id` int(11) unsigned NOT NULL auto_increment,
	  `name` varchar(255) NOT NULL default '',
	  `region` varchar(255) NOT NULL default '',
	  `email` varchar(255) NOT NULL default '',
	  `comment` text NOT NULL default '',
	  `subject` varchar(255) NOT NULL default '', 
	  `foundvia` varchar(255) NOT NULL default '', 
	  `used_products` varchar(255) NOT NULL default '', 
	  `status` smallint(6) NOT NULL default '0', 
	  `created_time` datetime NULL,
	  `update_time` datetime NULL,
	  PRIMARY KEY (`customcontact_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 