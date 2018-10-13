<?php 
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'netterm', array(
		'type'               => 'varchar',
        'label'              => 'Net Terms',
        'input'              => 'text',
        'required'           => false,
        'sort_order'         => 100,
        'visible'            => false,
        'system'             => false,
        'validate_rules'     => 'a:1:{s:15:"max_text_length";i:255;}',
        'position'           => 100,
        'admin_checkout'     => 1,
        'user_defined'      => true,
));
//$installer->addAttribute('customer', 'credit_limit', array(
//		'type'               => 'varchar',
//        'label'              => 'Credit Limit',
//        'input'              => 'text',
//        'required'           => false,
//        'sort_order'         => 100,
//        'visible'            => false,
//        'system'             => false,
//        'validate_rules'     => 'a:1:{s:15:"max_text_length";i:255;}',
//        'position'           => 100,
//        'admin_checkout'     => 1,
//));
  Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'netterm')
    ->setData('used_in_forms', array('adminhtml_customer','adminhtml_checkout')) 
    ->save(); 
//  Mage::getSingleton('eav/config')
//    ->getAttribute('customer', 'credit_limit') 
//    ->setData('used_in_forms', array('adminhtml_customer')) 
//    ->save();  
 
$installer->run(" 
	ALTER TABLE `{$installer->getTable('sales/order')}` ADD `customer_netterm` VARCHAR(255) NOT NULL;
	ALTER TABLE `{$installer->getTable('sales/order')}` ADD `credit_limit` INT(11) NOT NULL;
	ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `customer_netterm` VARCHAR(255) NOT NULL;
	ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `credit_limit` INT(11) NOT NULL;
	ALTER TABLE `{$installer->getTable('sales/quote')}` ADD `customer_netterm` VARCHAR(255) NOT NULL; 
	ALTER TABLE `{$installer->getTable('sales/quote')}` ADD `credit_limit` INT(11) NOT NULL;
");

$installer->run("
 
	ALTER TABLE `{$installer->getTable('sales/quote_payment')}` CHANGE `po_net_term` `netterm` VARCHAR( 255 ) NOT NULL ;
	ALTER TABLE `{$installer->getTable('sales/order_payment')}` CHANGE `po_net_term` `netterm` VARCHAR( 255 ) NOT NULL ;
	ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `credit_limit` INT( 11 ) NOT NULL ;
	ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `credit_limit` INT( 11 ) NOT NULL ;
 
");
 
$installer->endSetup();

