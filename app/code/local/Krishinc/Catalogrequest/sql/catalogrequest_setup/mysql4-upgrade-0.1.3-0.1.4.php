<?php

$installer = $this;

$installer->startSetup();
$sql = "ALTER TABLE {$this->getTable('catalogrequest')} ADD `catalog_code`  VARCHAR(255) NOT NULL DEFAULT ''";
$installer->run($sql);
$installer->endSetup();
?>