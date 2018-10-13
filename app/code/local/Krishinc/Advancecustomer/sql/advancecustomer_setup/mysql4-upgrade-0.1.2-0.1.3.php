<?php
$installer = $this;
$installer->startSetup();
 //Check if the attribute exists. If not, then we proceed to add it
if($idAttr = $installer->getAttributeId('customer', 'customer_type')) {

	$installer->run("DELETE FROM `customer_form_attribute` WHERE `customer_form_attribute`.`attribute_id` = $idAttr;");
	$installer->removeAttribute('customer', 'customer_type');	
} 
$installer->endSetup(); 