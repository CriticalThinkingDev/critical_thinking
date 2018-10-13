<?php 
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->run(" 
	ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD  `check_number` VARCHAR( 255 ) NOT NULL;
	ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD  `check_number` VARCHAR( 255 ) NOT NULL;   
");