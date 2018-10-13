<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Downloadable Link resource model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.0
 */

class Pisc_Downloadable_Model_Mysql4_Link extends Mage_Downloadable_Model_Mysql4_Link
{
    /**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
        parent::_construct();
    }

    /*
     * Returns the Link Title
     */
    public function getLinkTitle($linkObject)
    {
    	$result = null;

        $sql = $this->_getReadAdapter()->select()
            	->from($this->getTable('downloadable/link_title'))
            	->where('link_id = ?', $linkObject->getLinkId())
            	->where('store_id = ?', $linkObject->getStoreId());

         $result = $this->_getReadAdapter()->fetchOne($sql);

         return $result;
    }

}
