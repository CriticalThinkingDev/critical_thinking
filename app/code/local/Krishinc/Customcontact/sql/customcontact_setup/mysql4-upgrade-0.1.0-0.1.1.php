<?php
	$installer = $this;
    $installer->startSetup();

$installer->run("ALTER TABLE `{$this->getTable('customcontact')}` ADD `zipcode` VARCHAR( 255 ) NOT NULL;  ");
  	$installer->endSetup();
?>
