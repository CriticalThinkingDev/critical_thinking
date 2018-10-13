<?php

$installer = $this;

$installer->startSetup();

$installer->run("	
	-- DROP TABLE IF EXISTS {$this->getTable('fieldtester')};
	CREATE TABLE {$this->getTable('fieldtester')} (
	  `fieldtester_id` int(11) unsigned NOT NULL auto_increment,
	  `firstname` varchar(255) NOT NULL default '',
	  `lastname` varchar(255) NOT NULL default '', 
	  `address1` varchar(255) NOT NULL default '',
	  `address2` varchar(255) NOT NULL default '',
	  `city` varchar(255) NOT NULL default '',
	  `region_id` varchar(255) NOT NULL default '',
	  `region` varchar(255) NOT NULL default '',
	  `zipcode` varchar(255) NOT NULL default '',
	  `country` varchar(255) NOT NULL default '',
	  `email` varchar(255) NOT NULL default '',
	  `phone` varchar(255) NOT NULL default '',
	  `best_describe` varchar(255) NOT NULL default '', 
	  `status` smallint(6) NOT NULL default '0',
	  `created_time` datetime NULL,
	  `update_time` datetime NULL,
	  PRIMARY KEY (`fieldtester_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 