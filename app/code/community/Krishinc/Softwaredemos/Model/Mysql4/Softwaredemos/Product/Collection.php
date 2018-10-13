<?php

class Krishinc_Softwaredemos_Model_Mysql4_Softwaredemos_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{ 
    
    /**
     * Class constructor
     *
     */
   

    
    public function _construct()
    {
        parent::_construct();
        $this->_init('softwaredemos/softwaredemos_product','softwaredemos_product_id');
    }
       /**
     * Set store Id
     *
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Category
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }
    
     /**
     * Get positions of associated to category products
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getProductsPosition($softwaredemos)
    {
        $select = $this->_getWriteAdapter()->select()
          		  ->from($this->getTable('softwaredemos/softwaredemos_product'), array('product_id', 'position'))
          		  ->where('softwaredemos_id = :softwaredemos_id'); 
        $bind = array('softwaredemos_id' => (int)$softwaredemos->getId());  

        return $this->_getWriteAdapter()->fetchPairs($select, $bind);  
    }
    
}