<?php
 	 
    $installer = $this;
    $installer->startSetup();
    $installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('award')};
    CREATE TABLE {$this->getTable('award')} (
     `award_id` int(11) unsigned NOT NULL auto_increment,
     `award_option_id` int(11) NOT NULL default '0',
     `description` text NOT NULL default '', 
     `name` varchar(200) NOT NULL default '',       
     `image` varchar(255) NOT NULL default '',
      PRIMARY KEY (`award_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
   "); 
    $installer->installEntities(); 
 	$installer->endSetup();
