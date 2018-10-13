<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('softwaredemos')};
	CREATE TABLE {$this->getTable('softwaredemos')} (
	  `softwaredemos_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `softname` varchar(255) NOT NULL DEFAULT '',
	  `subject_id` int(11) NOT NULL DEFAULT '0',
	  `icon_img` varchar(255) NOT NULL DEFAULT '',
	  `thumbline_img` varchar(255) NOT NULL DEFAULT '',
	  `large_img` varchar(255) NOT NULL DEFAULT '',
	  `description` text NOT NULL,
	  `softwaredemo_product_id` text NOT NULL,
	  `status` smallint(6) NOT NULL DEFAULT '0',
	  `created_time` datetime DEFAULT NULL,
	  `update_time` datetime DEFAULT NULL,
	  PRIMARY KEY (`softwaredemos_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    "); 

$installer->endSetup(); 