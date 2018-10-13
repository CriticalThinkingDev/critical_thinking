<?php

 	$installer = $this;
    $installer->startSetup();
    $installer->run("ALTER TABLE `{$this->getTable('pressrelease')}` ADD `pagelink` VARCHAR( 255 ) NOT NULL; "); 
 
 	$installer->endSetup();
?>