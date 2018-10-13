<?php

$installer = $this;

$installer->startSetup();
  
$installer->run("ALTER TABLE `{$this->getTable('softwaredemos')}` ADD `grades` varchar(100)  DEFAULT '' NOT NULL after `description`;  ");   
  
 
$installer->endSetup();