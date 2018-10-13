<?php

$installer = $this;

$installer->startSetup();

$installer->run("
 
	ALTER TABLE {$this->getTable('sales_flat_quote_item')} ADD `catalogue_item` int(1) NULL DEFAULT NULL;
	ALTER TABLE {$this->getTable('sales_flat_order_item')} ADD `catalogue_item` int(1) NULL DEFAULT NULL; 
	ALTER TABLE {$this->getTable('sales_flat_invoice_item')} ADD `catalogue_item` int(1) NULL DEFAULT NULL; 
	ALTER TABLE {$this->getTable('sales_flat_shipment_item')} ADD `catalogue_item` int(1) NULL DEFAULT NULL;  
	ALTER TABLE {$this->getTable('sales_flat_creditmemo_item')} ADD `catalogue_item` int(1) NULL DEFAULT NULL;  

");

$installer->endSetup(); 