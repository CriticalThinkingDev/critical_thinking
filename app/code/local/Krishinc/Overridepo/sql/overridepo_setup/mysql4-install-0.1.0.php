<?php
$installer = $this;
$installer->startSetup();
$installer->run("
 
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `po_net_term` INT( 11 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `po_net_term` INT( 11 ) NOT NULL ;

 
");
$installer->endSetup();