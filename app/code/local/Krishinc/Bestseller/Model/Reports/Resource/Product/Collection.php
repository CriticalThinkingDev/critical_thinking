<?php
class Krishinc_Bestseller_Model_Reports_Resource_Product_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
	 /**
     * Add ordered qty's
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addOrderedQty($from = '', $to = '')
    {
        $adapter              = $this->getConnection();
        $compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );
        
//echo $this->getProductEntityTableName();exit;
        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($from != '' && $to != '') {
            $fieldName            = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }

        $this->getSelect()->reset()
            ->from( 
                array('order_items' => $this->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name'
                ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array());
        if ($this->isEnabledFlat()) {
        	 $this->getSelect()->joinLeft(
	                array('e' => Mage::getResourceSingleton('catalog/product_flat')->getFlatTableName()),
	               "e.entity_id = order_items.product_id"  );
        } else {
	          $this->getSelect()->joinLeft(
	                array('e' => $this->getProductEntityTableName()),
	                implode(' AND ', $productJoinCondition),
	                array(
	                    'entity_id' => 'order_items.product_id',
	                    'entity_type_id' => 'e.entity_type_id',
	                    'attribute_set_id' => 'e.attribute_set_id',
	                    'type_id' => 'e.type_id',
	                    'sku' => 'e.sku',
	                    'has_options' => 'e.has_options',
	                    'required_options' => 'e.required_options',
	                    'created_at' => 'e.created_at',
	                    'updated_at' => 'e.updated_at'
	                ));
	        }
            $this->getSelect()->where('parent_item_id IS NULL')
            ->group('order_items.product_id')
            ->having('SUM(order_items.qty_ordered) > ?', 0);
        return $this;
    }
}