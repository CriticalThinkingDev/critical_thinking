<?php

$installer = $this;

$installer->startSetup();

$installer->run("	
	-- DROP TABLE IF EXISTS {$this->getTable('dealerskit')};
	CREATE TABLE {$this->getTable('dealerskit')} (
	  `dealerskit_id` int(11) unsigned NOT NULL auto_increment, 
	  `company` varchar(255) NOT NULL default '',
	  `attention` varchar(255) NOT NULL default '',
	  `address1` varchar(255) NOT NULL default '',
	  `address2` varchar(255) NOT NULL default '',
	  `city` varchar(255) NOT NULL default '',
	  `region_id` varchar(255) NOT NULL default '',
	  `region` varchar(255) NOT NULL default '',
	  `zipcode` varchar(255) NOT NULL default '',
	  `country` varchar(255) NOT NULL default '',
	  `email` varchar(255) NOT NULL default '',
	  `phone` varchar(255) NOT NULL default '', 
	  `status` smallint(6) NOT NULL default '0',
	  `created_time` datetime NULL,
	  `update_time` datetime NULL,
	  PRIMARY KEY (`dealerskit_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 