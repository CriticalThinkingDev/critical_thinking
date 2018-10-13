<?php
	$installer = $this;
    $installer->startSetup(); 
    $installer->run("ALTER TABLE `{$this->getTable('dealerskit')}` ADD `listrak_response` VARCHAR( 255 ) NOT NULL;  "); 
  	$installer->endSetup();
?>