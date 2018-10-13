<?php

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE {$this->getTable('giftcard_entity')} (
  `giftcard_id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(255) DEFAULT NULL,
  `pin` int(4) DEFAULT NULL,
  `bal` float(12,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`giftcard_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('giftcard_payment')} (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `giftcard_id` int(11) DEFAULT NULL,
  `quote_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `amount` decimal(12,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('sales_flat_quote_item')} ADD `is_giftcard` int(1) NULL DEFAULT NULL;
ALTER TABLE {$this->getTable('sales_flat_quote_item')} ADD `giftcard_pin` int NULL DEFAULT NULL;
ALTER TABLE {$this->getTable('sales_flat_quote_item')} ADD `giftcard_num` varchar(255) NULL DEFAULT NULL;
ALTER TABLE {$this->getTable('sales_flat_quote_item')} ADD `giftcard_email` varchar(255) NULL DEFAULT NULL;

INSERT INTO {$this->getTable('giftcard_entity')} (`giftcard_id`,`number`,`pin`,`bal`,`created_at`,`updated_at`)
VALUES (NULL, '12341234', 1234, 500.0000, NULL, NULL);

")->endSetup();