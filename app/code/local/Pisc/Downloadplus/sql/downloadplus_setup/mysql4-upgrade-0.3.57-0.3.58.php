<?php
$installer = $this;
$installer->startSetup();

// Check for Table Field
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_extension'), 'serial_number_pool_unlock')) {
    $installer->run("
        ALTER TABLE {$this->getTable('downloadplus_link_extension')}
    ADD `serial_number_pool_unlock` varchar(255) DEFAULT NULL AFTER `serial_number_pool`
    ;
    ");
}

$installer->endSetup();
?>