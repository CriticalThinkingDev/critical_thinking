<?php
/**
 * Downloadplus Data Collection Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Data_Collection extends Varien_Data_Collection
{
	
	public function addItem(Varien_Object $item, $getId=false)
	{
		if ($getId) {
			$itemId = $this->_getItemId($item);
		} else {
			$itemId = $this->count()+1;
		}
	
		if (!is_null($itemId)) {
			if (isset($this->_items[$itemId])) {
				throw new Exception('Item ('.get_class($item).') with the same id "'.$item->getId().'" already exist');
			}
			$this->_items[$itemId] = $item;
		} else {
			$this->_addItem($item);
		}
		return $this;
	}
	
	public function getAllIds()
	{
		$ids = array_keys($this->getItems());
		return $ids;
	}
	
	public function getSize()
	{
		$this->load();
		if (is_null($this->_totalRecords)) {
			$this->_totalRecords = count($this->getItems());
		}
		return intval($this->_totalRecords);
	}
	
}