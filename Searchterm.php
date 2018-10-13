<?php
ini_set('memory_limit', '1028M');
require_once('app/Mage.php'); //Path to Magento
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);

Mage::app();

$collection = Mage::getModel('catalogsearch/query')->getCollection();

foreach($collection as $query){
   if($query->getNumResults() < 1){
       $query->setDisplayInTerms('0');
       $query->save();
   }
}