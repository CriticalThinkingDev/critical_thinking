<?php
 
$this->startSetup()->run(" ALTER TABLE {$this->getTable('ustorelocator_location')}   ADD `sort_order` INT( 11 ) NOT NULL AFTER `region` ,
ADD `status` SMALLINT( 6 ) NOT NULL DEFAULT '1' AFTER `sort_order` "
)->endSetup();
