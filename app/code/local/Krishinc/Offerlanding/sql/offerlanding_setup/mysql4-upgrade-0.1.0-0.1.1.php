<?php
	$installer = $this;
    $installer->startSetup(); 
    $installer->run("ALTER TABLE `{$this->getTable('offerlanding')}` ADD `listrak_response` VARCHAR( 255 ) NOT NULL;  "); 
    $installer->run("ALTER TABLE `{$this->getTable('offerlanding')}` ADD `supply` VARCHAR( 255 ) NOT NULL;  "); 
  	$installer->endSetup();
?>