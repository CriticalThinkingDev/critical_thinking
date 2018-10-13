<?php
$installer = $this;
$installer->startSetup();
 

 //Check if the attribute exists. If not, then we proceed to add it
if(!$idAttr = $installer->getAttributeId('customer_address', 'customer_type')) {
    // adds the attribute to the database
    $addAttr = $installer->addAttribute('customer_address', 'customer_type', array(
        'type'             => 'varchar',
	    'input' 	       => 'select',
	    'label' 	 	   => 'Customer Type',
	    'global' 	 	   => 1,
	    'visible' 	       => 1,
	    'required' 	   	   => 0,
	    'user_defined' 	   => 1,
	    'default' 	   	   => '0',
	    'is_system'		   => 0,
	    'visible_on_front' => 1,  
	    'source'           => 'Krishinc_Advancecustomer_Model_Source_Marketarray',
    ));
    
    
     $defaultUsedInForms = array(
	    'adminhtml_customer_address',
	    'customer_address_edit',
	    'customer_register_address'
	);
	/* @var $eavConfig Mage_Eav_Model_Config */
	 $eavConfig = Mage::getSingleton('eav/config');
	 $attribute = $eavConfig->getAttribute('customer_address',  'customer_type');
    
    if (false === ($attribute->getData('is_system') == 1 && $attribute->getData('is_visible') == 0)) {
        $attribute->setData('used_in_forms', $defaultUsedInForms); 
    }
    $attribute->save(); 

	$installer->run("ALTER TABLE  `{$this->getTable('sales/quote_address')}` ADD  `customer_type` Varchar(11) NOT NULL;");
	$installer->run("ALTER TABLE  `{$this->getTable('sales/order_address')}` ADD  `customer_type` Varchar(11) NOT NULL;");
}
?>