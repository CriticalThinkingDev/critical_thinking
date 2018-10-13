<?php

 	$installer = $this;
    $installer->startSetup();
    $installer->run("ALTER TABLE `{$this->getTable('award')}` ADD  `awarddate` datetime NULL;
   "); 
    
 	$installer->endSetup();
?>