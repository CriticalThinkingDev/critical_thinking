<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Product Serialnumber resource model
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author
 * @version    0.1.3
 */
class Pisc_Downloadplus_Model_Mysql4_Product_Serialnumber extends Mage_Core_Model_Mysql4_Abstract
{

	protected $_isPkAutoIncrement = false;
	
	protected $_filter = Array();
	
    /**
     * Initialize connection and define resource
     *
     */
    protected function _construct()
    {
    	$this->_init('downloadplus/product_serialnumber', 'serial_hash');
    	$this->clearFilter();
    }

    public function clearFilter()
    {
    	$this->_filter = Array();
    	return $this;
    }
    
    /*
     * Save function
     * Corrected for Not-Autoincrement primary key support
     */
    public function save(Mage_Core_Model_Abstract $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }

        $this->_beforeSave($object);
        $this->_checkUnique($object);

        if (!is_null($object->getId())) {
            $condition = $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $object->getId());
            /**
             * Not auto increment primary key support
             */
            if ($this->_isPkAutoIncrement) {
                $this->_getWriteAdapter()->update($this->getMainTable(), $this->_prepareDataForSave($object), $condition);
            } else {
                $select = $this->_getWriteAdapter()->select(array($this->getIdFieldName()))
                									->from($this->getMainTable())
                									->where($condition);
                if ($this->_getWriteAdapter()->fetchOne($select) !== false) {
                    $this->_getWriteAdapter()->update($this->getMainTable(), $this->_prepareDataForSave($object), $condition);
                } else {
                    $this->_getWriteAdapter()->insert($this->getMainTable(), $this->_prepareDataForSave($object));
                }
            }
        } else {
            $this->_getWriteAdapter()->insert($this->getMainTable(), $this->_prepareDataForSave($object));
            $object->setId($this->_getWriteAdapter()->lastInsertId($this->getMainTable()));
        }

        $this->_afterSave($object);

        return $this;
    }

    public function addProductToFilter($product)
    {
    	if (is_object($product)) {
    		$this->_filter[] = 'product_id='.$product->getId();
    	}
    	if (is_int($product)) {
    		$this->_filter[] = 'product_id='.$product;
    	}
    	return $this;
    }

    public function addGlobalToFilter()
    {
   		$this->_filter[] = 'product_id IS NULL';
    	return $this;
    }
    
    /*
    * Returns selected serialnumber pools
    */
    public function getSerialnumberPools()
    {
    	$result = null;
    	 
    	$where = $this->_filter;
    	$where[] = "serial_number_pool IS NOT NULL";
    
    	$sql = $this->_getReadAdapter()
			    	->select()
			    	->distinct()
			    	->from($this->getTable('downloadplus/product_serialnumber'), 'serial_number_pool')
			    	->where(implode(' AND ', $where));
    	 
    	$result = $this->_getReadAdapter()->fetchCol($sql);

    	return $result;
    }
    
}
