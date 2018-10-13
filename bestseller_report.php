<?php
define('MAGENTO_ROOT', getcwd());

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
umask(0);
Mage::app('default');

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
 
$aggregationTables = array(
    Mage_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_DAILY,
    Mage_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_MONTHLY,
    Mage_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_YEARLY,
);  

foreach ($aggregationTables as $table) {
	$table = 'sales_bestsellers_aggregated_'.$table; 
	
    $sql = "SELECT * FROM `$table` where `product_sku` IS NULL;";
	$data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
     echo '<br>------------ '.$table.' --------- <br>';
	 echo '<pre>';print_r(count($data)); 
	$i=0;
	$total = sizeof($data); 
		if($total > 0) {
			
			foreach ($data as $value) {  
				
				$sku = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne("SELECT `sku`  FROM `catalog_product_entity` WHERE `entity_id` = '".$value['product_id']."';");
				$sql = "UPDATE $table SET `product_sku`='".$sku."' WHERE `product_id`=".$value['product_id'].";";
				$installer->run($sql); 
				 
			}  
		
		}   
} 

 
$installer->endSetup();
?>

 