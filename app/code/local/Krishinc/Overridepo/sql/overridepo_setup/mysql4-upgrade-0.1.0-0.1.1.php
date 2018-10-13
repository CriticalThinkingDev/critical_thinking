<?php 
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->run(" 
ALTER TABLE `{$installer->getTable('sales/order')}` ADD `order_type` VARCHAR(255) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `order_type` VARCHAR(255) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/quote')}` ADD `order_type` VARCHAR(255) NOT NULL; 
");
$installer->endSetup();

