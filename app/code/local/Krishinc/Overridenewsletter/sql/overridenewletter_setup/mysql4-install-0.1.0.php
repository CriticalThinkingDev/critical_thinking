<?php
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `newsletter_subscriber` 
ADD `firstname` VARCHAR( 255 ) NOT NULL COMMENT 'firstname custom field' AFTER `customer_id` ,
ADD `lastname` VARCHAR( 255 ) NOT NULL COMMENT 'lastname custom field' AFTER `firstname`, 
ADD `position` VARCHAR( 255 ) NOT NULL COMMENT 'position custom field' AFTER `lastname` ,
ADD `listrak_response` VARCHAR( 255 ) NOT NULL COMMENT 'listrak_response custom field' 
");
$installer->endSetup();
 
?>