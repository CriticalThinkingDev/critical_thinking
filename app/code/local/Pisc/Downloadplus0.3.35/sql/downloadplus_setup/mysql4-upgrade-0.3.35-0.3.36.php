<?php
$installer = $this;
$installer->startSetup();

// Correction of DB Table
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_customer_item'), 'purchased_id')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_link_customer_item')}
		MODIFY COLUMN `purchased_id` int(10) unsigned DEFAULT NULL
		;
	");
	$installer->run("
		UPDATE {$this->getTable('downloadplus_link_customer_item')}
		SET `purchased_id`=NULL
		WHERE `purchased_id`=0
		;
	");
}
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_customer_item'), 'order_item_id')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_link_customer_item')}
		MODIFY COLUMN `order_item_id` int(10) unsigned DEFAULT NULL
		;
	");
	$installer->run("
		UPDATE {$this->getTable('downloadplus_link_customer_item')}
		SET `order_item_id`=NULL
		WHERE `order_item_id`=0
		;
	");
}
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_customer_item'), 'product_id')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_link_customer_item')}
		MODIFY COLUMN `product_id` int(10) unsigned DEFAULT NULL
		;
	");
	$installer->run("
		UPDATE {$this->getTable('downloadplus_link_customer_item')}
		SET `product_id`=NULL
		WHERE `product_id`=0
		;
	");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'purchased_item_id')) {
	$installer->run("
			ALTER TABLE {$this->getTable('downloadplus_link_purchased_item_serialnumber')}
			ADD COLUMN `purchased_item_id` int(10) unsigned DEFAULT NULL AFTER `purchased_id`
			;
	");
}
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_link_purchased_item_serialnumber'), 'purchased_item_id')) {
	$collection = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->getCollection();
	foreach ($collection as $item) {
		if ($item->getPurchasedId() && !$item->getPurchasedItemId()) {
			$links = Mage::getModel('downloadplus/link_purchased_item')->getCollection()
						->addFieldToFilter('purchased_id', array('eq'=>$item->getPurchasedId()))
						->addFieldToFilter('link_id', array('eq'=>$item->getLinkId()));
			
			foreach ($links as $link) {
				if ($link->getPurchasedId()==$item->getPurchasedId() && $link->getLinkId()==$item->getLinkId()) {
					$item->setPurchasedItemId($link->getId());
					$item->save();
					break;
				}
			}
		}
	}
}


$installer->endSetup();
?>