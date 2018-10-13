<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

try {
    $installer->run("
        ALTER TABLE `{$installer->getTable('salesrule')}`
        ADD COLUMN `fixed_shipping_amount` decimal(12,4) NULL AFTER `simple_free_shipping`;
    ");
} catch (Exception $e) {
    //
}

$installer->endSetup();