<?php
$installer = $this;
$installer->startSetup();

// Check for Table Field
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'customer_notified_at')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
		ADD `customer_notified_at` datetime DEFAULT NULL AFTER `updated_at`
		;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'administrator_notified_at')) {
    $installer->run("
        ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
    ADD `administrator_notified_at` datetime DEFAULT NULL AFTER `customer_notified_at`
    ;
    ");
}

$installer->endSetup();
?>