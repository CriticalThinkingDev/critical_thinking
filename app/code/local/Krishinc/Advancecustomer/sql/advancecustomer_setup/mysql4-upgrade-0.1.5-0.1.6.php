<?php
$installer = $this;
$installer->startSetup();
 

 //Check if the attribute exists. If not, then we proceed to add it
//if(!$idAttr = $installer->getAttributeId('customer_address', 'street1')) {
//     adds the attribute to the database
//    $addAttr = $installer->addAttribute('customer_address', 'street1', array(
//        'type'                => 'varchar',
//        'input'                => 'text',
//        'default'            => 0, 
//        'label'             => 'Street1',
//        'required'             => 0,
//        'user_defined'        => 1,
//        'default'               => '0',
//        'is_system'           => 0,
//        'visible_on_front' => 0,  
//        
//    ));
//} 

// Check if the attribute exists. If not, then we proceed to add it
//if(!$idAttr = $installer->getAttributeId('customer_address', 'street2')) {
//     adds the attribute to the database
//    $addAttr = $installer->addAttribute('customer_address', 'street2', array(
//        'type'                => 'varchar',
//        'input'                => 'text',
//        'default'            => 0, 
//        'label'             => 'Street2',
//        'required'             => 0,
//        'user_defined'        => 1,
//        'default'               => '0',
//        'is_system'           => 0,
//        'visible_on_front' => 0,  
//        
//    ));
//}
$installer->updateAttribute('customer_address','street1', 'attribute_code' , 'address_street1');    
$installer->updateAttribute('customer_address','street2', 'attribute_code' , 'address_street2');    
    ?>