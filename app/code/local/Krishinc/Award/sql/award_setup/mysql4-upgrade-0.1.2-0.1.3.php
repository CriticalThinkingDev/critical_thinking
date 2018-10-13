<?php

 	$installer = $this;
    $installer->startSetup();
    $installer->run("ALTER TABLE `{$this->getTable('award')}` ADD  `is_companyaward` smallint(6) NOT NULL default '2';
   "); 
    
 	$installer->endSetup();
?>