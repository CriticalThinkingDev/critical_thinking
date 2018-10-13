<?php
class Krishinc_Overridepo_Model_Resource_Order extends Mage_Sales_Model_Resource_Order 
{
	 /**
     * Init virtual grid records for entity
     *
     * @return Mage_Sales_Model_Resource_Order
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
		$adapter = $this->getReadConnection();
  
        $ifnullCompany    = $adapter->getIfNullSql('{{table}}.company', $adapter->quote('')); 
        $concatAddressCompany = $adapter->getConcatSql(array($ifnullCompany)); 
        $this->addVirtualGridColumn(
                 'billing_company',
                'sales/order_address',
                array('billing_address_id' => 'entity_id'),
                $concatAddressCompany 
            );  
        return $this;
    }
}