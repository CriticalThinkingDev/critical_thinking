<?php
class Krishinc_Reviews_Model_Resource_Review_Product_Collection extends Mage_Review_Model_Resource_Review_Product_Collection
{
	 /**
     * join fields to entity
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _joinFields()
    {
        $reviewTable = Mage::getSingleton('core/resource')->getTableName('review/review');
        $reviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review/review_detail');

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('sku');

        $this->getSelect()
            ->join(array('rt' => $reviewTable),
                'rt.entity_pk_value = e.entity_id',
                array('rt.review_id', 'review_created_at'=> 'rt.created_at', 'rt.entity_pk_value', 'rt.status_id'))
            ->join(array('rdt' => $reviewDetailTable),
                'rdt.review_id = rt.review_id',
                array('rdt.title','rdt.nickname', 'rdt.detail', 'rdt.location', 'rdt.customer_id', 'rdt.store_id'));
        return $this;
    }
    
     /**
     * Set order to attribute
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function setOrder($attribute, $dir = 'DESC')
    {
        switch($attribute) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.location':
            case 'rdt.detail':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
            case 'stores':
                // No way to sort
                break;
            case 'type':
                $this->getSelect()->order('rdt.customer_id ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
        }
        return $this;
    }
    
 	/**
     * Add attribute to filter
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        switch($attribute) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.location':
            case 'rdt.detail':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                break;
            case 'stores':
                $this->setStoreFilter($condition);
                break;
            case 'type':
                if ($condition == 1) {
                    $conditionParts = array(
                        $this->_getConditionSql('rdt.customer_id', array('is' => new Zend_Db_Expr('NULL'))),
                        $this->_getConditionSql('rdt.store_id', array('eq' => Mage_Core_Model_App::ADMIN_STORE_ID))
                    );
                    $conditionSql = implode(' AND ', $conditionParts);
                } elseif ($condition == 2) {
                    $conditionSql = $this->_getConditionSql('rdt.customer_id', array('gt' => 0));
                } else {
                    $conditionParts = array(
                        $this->_getConditionSql('rdt.customer_id', array('is' => new Zend_Db_Expr('NULL'))),
                        $this->_getConditionSql('rdt.store_id', array('neq' => Mage_Core_Model_App::ADMIN_STORE_ID))
                    );
                    $conditionSql = implode(' AND ', $conditionParts);
                }
                $this->getSelect()->where($conditionSql);
                break;

            default:
                parent::addAttributeToFilter($attribute, $condition, $joinType);
                break; 
        }
        return $this;
    }
}