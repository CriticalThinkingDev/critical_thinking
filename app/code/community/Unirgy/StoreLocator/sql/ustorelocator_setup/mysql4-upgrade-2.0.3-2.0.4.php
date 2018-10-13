<?php
 

$this->startSetup()->run(" ALTER TABLE {$this->getTable('ustorelocator_location')}  DROP COLUMN `sort_order` ,
 ADD COLUMN `city` VARCHAR(255) NOT NULL AFTER `status` "
)->endSetup();
