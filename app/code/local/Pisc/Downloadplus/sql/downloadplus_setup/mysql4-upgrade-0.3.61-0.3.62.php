<?php
$installer = $this;
$installer->startSetup();

// Check for Table Field
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_extension'), 'attributes')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_extension')}
		ADD `attributes` text AFTER `expires_on`
		;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_extension'), 'unlock_serial_number')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_extension')}
		ADD `unlock_serial_number` text AFTER `attributes`
		;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_extension'), 'unlock_serial_number_hash')) {
    $installer->run("
        ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_extension')}
        ADD `unlock_serial_number_hash` varchar(255) DEFAULT NULL AFTER `unlock_serial_number`
    ;
    ");
}

$installer->endSetup();
?>