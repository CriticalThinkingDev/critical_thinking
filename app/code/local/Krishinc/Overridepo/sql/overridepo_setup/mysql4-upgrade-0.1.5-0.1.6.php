<?php 
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->run(" 
 
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `billing_company` VARCHAR(255) NOT NULL;
 
");
$installer->endSetup();

