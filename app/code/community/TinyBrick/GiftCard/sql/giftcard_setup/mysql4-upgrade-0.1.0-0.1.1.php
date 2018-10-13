<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('giftcard_entity')} DROP `pin`;
ALTER TABLE {$this->getTable('sales_flat_quote_item')} ADD `giftcard_msg` varchar(3000) NULL DEFAULT NULL;

")->endSetup();