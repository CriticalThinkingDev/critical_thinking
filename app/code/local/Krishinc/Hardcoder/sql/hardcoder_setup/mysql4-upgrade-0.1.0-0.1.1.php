<?php

 	$installer = $this;
    $installer->startSetup();
    $installer->run("ALTER TABLE `{$this->getTable('hardcoder')}` ADD `p_id` VARCHAR( 255 ) NOT NULL;
   ");

 	$installer->endSetup();
?>
