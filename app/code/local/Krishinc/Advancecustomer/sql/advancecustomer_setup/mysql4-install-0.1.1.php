<?php
$installer = $this;
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'customer_type', array( 
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
if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
      $customer = Mage::getModel('customer/customer');
      $attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
      $setup->addAttributeToSet('customer', $attrSetId, 'General', 'customer_type');
}
if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'customer_type')
    ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register')) 
    ->save(); 
} 
$installer->run("ALTER TABLE  `{$this->getTable('sales/quote')}` ADD  `customer_type` Varchar(11) NOT NULL;");
$installer->run("ALTER TABLE  `{$this->getTable('sales/order')}` ADD  `customer_type` Varchar(11) NOT NULL;");
$installer->endSetup();