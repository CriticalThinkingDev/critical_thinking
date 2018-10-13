<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  PILLWAX Industrial Solutions Consulting
 * @version    0.1.1
 */

class Pisc_Downloadplus_Helper_Eav extends Mage_Core_Helper_Abstract
{
	protected $_eavJoinIndex = 1;

	public function joinEavTablesIntoCollection($collection, $mainTableForeignKey, $eavType, $eavAttribute=null, $mainTableAlias=null){

		/**
		 * Based upon: CodeMagento - http://codemagento.com/2011/03/joining-an-eav-table-to-flat-table/
		 * This assumes that the foreign key is the entity id of the eav table.
		 * $collection is a collection object of a flat table.
		 * $mainTableForeignKey is the name of the foreign key to the eav table.
		 * $eavType is the type of entity (the entity_type_code in eav_entity_type)
		 */

        $entityType = Mage::getModel('eav/entity_type')->loadByCode($eavType);
        $attributes = $entityType->getAttributeCollection();
        $entityTable = $collection->getTable($entityType->getEntityTable());

        //Use an incremented index to make sure all of the aliases for the eav attribute tables are unique.
        $staticFields = Array();
        is_null($mainTableAlias)?$mainTable='main_table.':$mainTable=$mainTableAlias;

        foreach ($attributes->getItems() as $attribute){
        	if (is_null($eavAttribute) || (!is_null($eavAttribute) && ($eavAttribute==$attribute->getAttributeCode())) || (is_array($eavAttribute) && in_array($attribute->getAttributeCode(), $eavAttribute)))
        	{
	            if ($attribute->getBackendType()!='static'){
	            	$alias = 'table'.$this->_eavJoinIndex;
	                $table = $entityTable. '_'.$attribute->getBackendType();
	                $field = $alias.'.value';
	                $collection->getSelect()
	                	->joinLeft(Array($alias => $table),
	                       $mainTable.$mainTableForeignKey.' = '.$alias.'.entity_id and '.$alias.'.attribute_id = '.$attribute->getAttributeId(),
	                		Array($attribute->getAttributeCode() => $field)
	                		);
	                $this->_eavJoinIndex++;
	            } else {
	            	$staticFields[$attribute->getAttributeCode()] = $entityTable.'.'.$attribute->getAttributeCode();
	            }
        	}
        }
        //Join in all of the static attributes by joining the base entity table.
        if (count($staticFields)>0) {
        	$collection->getSelect()->joinLeft($entityTable, $mainTable.$mainTableForeignKey.' = '.$entityTable.'.entity_id', $staticFields);
        }

        return $collection;
    }

    public function getEavTableJoin($mainTableForeignKey, $eavType, $eavAttribute=null, $mainTableAlias=null){
    	/**
    	 * Based upon: CodeMagento - http://codemagento.com/2011/03/joining-an-eav-table-to-flat-table/
    	 * This assumes that the foreign key is the entity id of the eav table.
    	 * $collection is a collection object of a flat table.
    	 * $mainTableForeignKey is the name of the foreign key to the eav table.
    	 * $eavType is the type of entity (the entity_type_code in eav_entity_type)
    	 */
    
    	$entityType = Mage::getModel('eav/entity_type')->loadByCode($eavType);
    	$attributes = $entityType->getAttributeCollection();
    	$entityTable = Mage::getSingleton('core/resource')->getTableName($entityType->getEntityTable());
		$sql = '';
    	
    	//Use an incremented index to make sure all of the aliases for the eav attribute tables are unique.
    	$staticFields = Array();
    	is_null($mainTableAlias)?$mainTable='main_table.':$mainTable=$mainTableAlias;
    
    	foreach ($attributes->getItems() as $attribute){
    		if (is_null($eavAttribute) || (!is_null($eavAttribute) && ($eavAttribute==$attribute->getAttributeCode())) || (is_array($eavAttribute) && in_array($attribute->getAttributeCode(), $eavAttribute)))
    		{
    			if ($attribute->getBackendType()!='static'){
    				$alias = 'table'.$this->_eavJoinIndex;
    				$table = $entityTable. '_'.$attribute->getBackendType();
    				$field = $alias.'.value';
    				$sql.= 'LEFT JOIN '.$table.' AS '.$alias.' ON '.$mainTable.$mainTableForeignKey.' = '.$alias.'.entity_id and '.$alias.'.attribute_id = '.$attribute->getAttributeId();
    				$this->_eavJoinIndex++;
    			} else {
    				$staticFields[$attribute->getAttributeCode()] = $entityTable.'.'.$attribute->getAttributeCode();
    			}
    		}
    	}
    	//Join in all of the static attributes by joining the base entity table.
    	if (count($staticFields)>0) {
    		$sql.= 'LEFT JOIN '.$entityTable.' ON '.$mainTable.$mainTableForeignKey.' = '.$entityTable.'.entity_id';
    	}
    
    	return $sql;
    }
    
    /*
     * Return real column name from select for aliases
     */
    public function getSelectColumnName($collection, $name)
    {
    	$result = $name;
    	$columns = $collection->getSelect()->getPart(Zend_Db_Select::COLUMNS);

    	if (is_array($columns)) {
    		foreach ($columns as $column) {
    			if (isset($column[2]) && $column[2]==$name) {
    				$result = $column[0].'.'.$column[1];
    			}
    		}
    	}

    	return $result;
    }

    /*
     * Add Customer Name to select
     */
    public function addCustomerNameToSelect($collection)
    {
        $fields = array();
        foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
            if ($node->is('name')) {
                //$fields[$code] = $code;
                $fields[$code] = $this->getSelectColumnName($collection, $code);
            }
        }

        $expr = 'CONCAT('
            .(isset($fields['prefix']) ? 'IF('.$fields['prefix'].' IS NOT NULL AND '.$fields['prefix'].'!="", CONCAT('.$fields['prefix'].'," "), ""),' : '')
            .$fields['firstname'].(isset($fields['middlename']) ?  ',IF('.$fields['middlename'].' IS NOT NULL AND '.$fields['middlename'].'!="", CONCAT(" ",'.$fields['middlename'].'), "")' : '').'," ",'.$fields['lastname']
            .(isset($fields['suffix']) ? ',IF('.$fields['suffix'].' IS NOT NULL AND '.$fields['suffix'].'!="", CONCAT(" ",'.$fields['suffix'].'), "")' : '')
        .') as name';

        $collection->getSelect()->from(null, $expr);

        return $collection;
    }

}

?>