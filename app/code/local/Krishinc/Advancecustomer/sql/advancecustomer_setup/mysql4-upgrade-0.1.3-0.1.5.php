<?php
$installer = $this;
$installer->startSetup();
 

 //Check if the attribute exists. If not, then we proceed to add it
if(!$idAttr = $installer->getAttributeId('customer_address', 'nomail')) {
    // adds the attribute to the database
    $addAttr = $installer->addAttribute('customer_address', 'nomail', array(
        'type'                => 'int',
	    'input'                => 'boolean',
	    'default'            => 0,
	    'source'            => 'eav/entity_attribute_source_boolean',
	    'label'             => 'Do Not Mail',
	    'required'             => 0,
	    'user_defined' 	   => 1,
	    'default' 	   	   => '0',
	    'is_system'		   => 0,
	    'visible_on_front' => 1,  
	    
    ));
    
    
     $defaultUsedInForms = array(
	    'adminhtml_customer_address',
	    'customer_address_edit',
	    'customer_register_address'
	);
	/* @var $eavConfig Mage_Eav_Model_Config */
	 $eavConfig = Mage::getSingleton('eav/config'); 
	 $attribute = $eavConfig->getAttribute('customer_address',  'nomail');
    
    if (false === ($attribute->getData('is_system') == 1 && $attribute->getData('is_visible') == 0)) {
        $attribute->setData('used_in_forms', $defaultUsedInForms); 
    }
    $attribute->save(); 

	$installer->run("ALTER TABLE  `{$this->getTable('sales/quote_address')}` ADD  `nomail` int(1) NOT NULL default '0';");
	$installer->run("ALTER TABLE  `{$this->getTable('sales/order_address')}` ADD  `nomail` int(1) NOT NULL default '0';");
}
?>