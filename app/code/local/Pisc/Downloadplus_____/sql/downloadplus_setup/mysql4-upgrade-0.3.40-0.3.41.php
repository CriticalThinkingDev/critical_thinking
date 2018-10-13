<?php
$installer = $this;
$installer->startSetup();

// Introduced with 0.3.41
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_log'), 'product_item_id')) {
	$installer->run("
			ALTER TABLE {$this->getTable('downloadplus_log')}
				ADD `product_item_id` int(11) DEFAULT NULL AFTER `sample_id`
			;
			");
}
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_log'), 'customer_item_id')) {
	$installer->run("
			ALTER TABLE {$this->getTable('downloadplus_log')}
				ADD `customer_item_id` int(11) DEFAULT NULL AFTER `product_item_id`
			;
			");
}
if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_log'), 'customer_id')) {
	$installer->run("
			ALTER TABLE {$this->getTable('downloadplus_log')}
				ADD `customer_id` int(11) DEFAULT NULL AFTER `log_id`
			;
			");
}
$table = $installer->getConnection()->describeTable($this->getTable('downloadplus_log'));
if (isset($table['timestamp']) && $table['timestamp']['DATA_TYPE']!='datetime') {
	if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_log'), 'datetime')) {
		$installer->run("
				ALTER TABLE {$this->getTable('downloadplus_log')}
					ADD `datetime` datetime DEFAULT NULL AFTER `ip`
				;
				");
		
	}
	
	// Update log with Customer Id and transfer Timestamp to new Column Type
	$collection = Mage::getModel('downloadplus/log')->getCollection();
	foreach ($collection as $item) {
		if ($id = $item->isPurchasedLink()) {
			$link = $item->getPurchasedLink();
			if ($link->getId() && $link->getCustomerId()) {
				$item->setCustomerId($link->getCustomerId());
			}
		}
		if ($item->getData('timestamp')) {
			$item->setData('datetime', date('Y-m-d H:m:s', $item->getData('timestamp')));
		} else {
			$item->setData('datetime', null);
		}
		$item->save();
	}
	
	// Exchange Timestamp Column with new format
	if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_log'), 'datetime')) {
		if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_log'), 'timestamp')) {
			$installer->run("
					ALTER TABLE {$this->getTable('downloadplus_log')}
						DROP COLUMN `timestamp`
					;
					");
		}
		$installer->run("
				ALTER TABLE {$this->getTable('downloadplus_log')}
					CHANGE `datetime` `timestamp` datetime
				;
				");
	}
}

// Adding Product Attribute for Access Control to Product Additional Downloads
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
if ($setup->getAttribute('catalog_product', 'downloadable_additional_clogin')) {
	$setup->removeAttribute('catalog_product', 'downloadable_additional_clogin');
}

$setup->addAttribute('catalog_product', 'downloadable_additional_clogin', array(
		'label'		=>	'Download requires registration',
		'type'		=>	'text',
		'input'		=>	'boolean',
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

if ($attribute = $setup->getAttribute('catalog_product','downloadable_additional_clogin')) {
	$attributeSetIds = $setup->getAllAttributeSetIds('catalog_product');
	foreach ($attributeSetIds as $attributeSetId) {
		try {
			$attributeGroupId = $setup->getAttributeGroupId('catalog_product', $attributeSetId, 'General');
		}
		catch(Exception $e) {
			$attributeGroupId = $setup->getDefaultAttributeGroupId('catalog/product', $attributeSetId);
		}
		$setup->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attribute['attribute_id']);
	}
}

$installer->endSetup();
?>