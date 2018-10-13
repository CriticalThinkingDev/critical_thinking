<?php
$installer = $this;
$installer->startSetup();

// Adding Product Attribute for Serialnumbers
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
if ($setup->getAttribute('catalog_product', 'downloadplus_serialnumbers_deactivate')) {
	$setup->removeAttribute('catalog_product', 'downloadplus_serialnumbers_deactivate');
}

$setup->addAttribute('catalog_product', 'downloadplus_serialnr_inactive', array(
		'label'		=>	'Deactivate Serialnumbers for this Product',
		'type'		=>	'text',
		'input'		=>	'select',
		'option'	=>	Array(
							'value' => Array('false'=>Array(0=>'No'), 'true'=>Array(0=>'Yes')),
							'sort_order' => Array('false'=>0, 'true'=>1)
						),
		'default' 	=> 0,
		'source'	=> '', 
		'backend'	=> '',
		'frontend'  => '',
		'visible'	=> false,
		'required'	=> false,
		'user_defined'	=> false,
		'searchable'	=> false,
		'filterable'	=> false,
		'comparable'	=> false,
		'is_configurable' => false,
		'visible_on_front'	=> false,
		'visible_in_advanced_search' => false,
		'is_html_allowed_on_front' => false,
		'used_for_price_rules' => false,
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();
?>