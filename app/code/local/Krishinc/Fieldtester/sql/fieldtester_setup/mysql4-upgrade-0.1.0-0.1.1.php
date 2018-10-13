<?php

$installer = $this;

$installer->startSetup();
$sql = "ALTER TABLE {$this->getTable('fieldtester')} CHANGE `country` `country_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$installer->run($sql);
$installer->endSetup();
?>