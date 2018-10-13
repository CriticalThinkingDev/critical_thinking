<?php

$installer = $this;

$installer->startSetup();
$sql = '';
$sql = "ALTER TABLE {$this->getTable('catalogrequest')} ADD `prospect`  VARCHAR(1) NOT NULL DEFAULT 'Y';";
$sql .= "ALTER TABLE {$this->getTable('catalogrequest')} ADD `is_export`  enum('Yes','No') NOT NULL DEFAULT 'No';";
$sql .= "ALTER TABLE {$this->getTable('catalogrequest')} ADD `export_at`  datetime NULL;";
$installer->run($sql);
$installer->endSetup();
?>