<?php

 	$installer = $this;
    $installer->startSetup();
    $installer->run("ALTER TABLE `{$this->getTable('award')}` ADD `award_url` VARCHAR( 255 ) NOT NULL;
   "); 
    $installer->installEntities(); 
 	$installer->endSetup();
?>