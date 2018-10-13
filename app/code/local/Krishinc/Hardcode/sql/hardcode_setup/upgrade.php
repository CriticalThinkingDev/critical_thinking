<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('rubyclub_homes')};
CREATE TABLE {$this->getTable('rubyclub_homes')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `documentNo` varchar(255) NOT NULL default '',
  `transactionDate` varchar(255) NOT NULL default '',
  `accountNo` varchar(255) NOT NULL default '',
  `points` varchar(255) NOT NULL default '',
  `expirationDate` varchar(255) NOT NULL default '',
  `storeCode` varchar(255) NOT NULL default '',
  `storeName` text NOT NULL default '',
  `memberName` varchar(255) NOT NULL default '',
  `email`varchar(255) NOT NULL default '',
  `mobilePhoneNo` varchar(255) NOT NULL default '',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();