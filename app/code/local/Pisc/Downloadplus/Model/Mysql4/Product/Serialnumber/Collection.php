<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Product Serialnumber Collection resource model
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author
 */
class Pisc_Downloadplus_Model_Mysql4_Product_Serialnumber_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
        parent::_construct();
    	$this->_init('downloadplus/product_serialnumber', 'serial_hash');
    }

    /*
     * Adds Product to Filter
     */
    public function addProductToFilter($product)
    {
    	$id = null;
    	if ($product instanceof Mage_Catalog_Model_Product) {
    		$id = $product->getId();
    	} else {
    		$id = $product;
    	}
    	if ($id) {
    		$this->getSelect()->where('product_id=?', $id);
    	}

    	return $this;
    }

    /*
     * Adds global to filter where product_id is null
     */
    public function addGlobalToFilter()
    {
    	$this->getSelect()->where('product_id IS NULL');
    	return $this;
    }

}
