<?php

$installer = $this;

$installer->startSetup();
 if (!$installer->tableExists($installer->getTable('softwaredemos'))) {
 
	$installer->run("ALTER TABLE {$this->getTable('softwaredemos')} DROP `softwaredemo_product_id`; ");
	
	$installer->run("CREATE TABLE {$this->getTable('softwaredemos_product')}(  
	  `softwaredemos_product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `softwaredemos_id` int(11) NOT NULL,
	  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
	  `position` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`softwaredemos_product_id`),
	  UNIQUE KEY `UNQ_SOFTWAREDEMOS_PRODUCT` (`softwaredemos_id`,`product_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
	");   
 } 
 
$installer->endSetup(); 