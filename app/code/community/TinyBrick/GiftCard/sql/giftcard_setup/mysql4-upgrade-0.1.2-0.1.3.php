<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('giftcard_payment')} ADD `is_admin` int(1) DEFAULT NULL;

")->endSetup();