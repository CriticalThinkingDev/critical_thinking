<?php

$installer = $this;

$installer->startSetup();

$installer->run("
 
	ALTER TABLE {$this->getTable('sales_flat_quote_item')} ADD `parent_bundle_id` varchar(10) NULL DEFAULT NULL;
	ALTER TABLE {$this->getTable('sales_flat_order_item')} ADD `parent_bundle_id` varchar(10) NULL DEFAULT NULL; 
");

$installer->endSetup(); 