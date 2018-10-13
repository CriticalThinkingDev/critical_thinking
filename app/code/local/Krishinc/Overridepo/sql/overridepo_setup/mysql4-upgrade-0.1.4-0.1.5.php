<?php 
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->addAttribute('order', 'is_taxexempt', array(
		'type'               => 'int',
        'label'              => 'IS Tax Exempt',
        'input'              => 'text',
        'required'           => false,
        'sort_order'         => 120,
        'visible'            => false,
        'system'             => false,
        'position'           => 100,
        'user_defined'      => true, 
)); 
$installer->run(" 
ALTER TABLE `{$installer->getTable('sales/order')}` ADD `is_taxexempt` VARCHAR(255) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/quote')}` ADD `is_taxexempt` VARCHAR(255) NOT NULL; 
"); 
$installer->endSetup();