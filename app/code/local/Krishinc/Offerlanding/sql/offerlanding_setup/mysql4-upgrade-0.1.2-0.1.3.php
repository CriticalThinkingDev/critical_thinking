<?php

$installer = $this;

$installer->startSetup();

$installer->run("	
	-- DROP TABLE IF EXISTS {$this->getTable('puzzle')};
	CREATE TABLE {$this->getTable('puzzle')} (
	  `puzzle_id` int(11) unsigned NOT NULL auto_increment,
	  `firstname` varchar(255) NOT NULL default '',
	  `lastname` varchar(255) NOT NULL default '', 
	   `email` varchar(255) NOT NULL default '',
	  
	  `best_describe` varchar(255) NOT NULL default '', 
	  `supply` varchar(255) NOT NULL default '',
	  `emaillist` text NOT NULL default '',
	  
	  `created_time` datetime NULL,
	  `update_time` datetime NULL,
	  PRIMARY KEY (`puzzle_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();