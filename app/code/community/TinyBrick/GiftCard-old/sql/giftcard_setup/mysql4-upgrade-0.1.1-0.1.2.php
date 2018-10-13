<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('giftcard_entity')} ADD `order_id` int(11) NULL DEFAULT NULL;
ALTER TABLE {$this->getTable('giftcard_entity')} ADD `type` int(1) NULL DEFAULT NULL;
ALTER TABLE {$this->getTable('giftcard_entity')} ADD `shipped` int(1) NULL DEFAULT NULL;
ALTER TABLE {$this->getTable('giftcard_entity')} ADD `order_amount` float(12,4) DEFAULT NULL;
ALTER TABLE {$this->getTable('sales_flat_quote_item')} ADD `is_gc_refill` int(1) NULL DEFAULT NULL;
ALTER TABLE {$this->getTable('giftcard_entity')} ADD `to_email` varchar(255) DEFAULT NULL;
ALTER TABLE {$this->getTable('giftcard_entity')} ADD `to_msg` varchar(3000) DEFAULT NULL;
ALTER TABLE {$this->getTable('giftcard_payment')} ADD `is_refill` int(1) DEFAULT NULL;

")->endSetup();