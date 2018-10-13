<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    1.2.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

abstract class AW_Marketsuite_Model_Attribute_Abstract extends AW_Marketsuite_Model_Object
{
    /**
     * Eav attribute table name
     * @var string
     */
    protected $eav_attribute_table;

    
    /**
     * Getting instance of product model according to magento version
     * @return mixed 
     */
    public static function getInstance()
    {
        return Mage::getModel('marketsuite/attribute_mag13');
    }

    public function __construct()
    {
        parent::__construct();
        $this->eav_attribute_table  = $this->resource->getTableName('eav/attribute');
    }

    /**
     *
     * @param string $attributeCode
     * @return AW_Marketsuite_Model_Attribute_Abstract 
     */
    public function getAttributeByCode($attributeCode)
    {
        $select = $this->getSelect()
            ->where('eav_attribute.attribute_code = ?', $attributeCode);

        $attribute = $this->conn_read->fetchRow($select);
        if ($attribute) $this->addData($attribute);
        return $this;
    }

    /**
     * Default select from DB. Getting all products
     * @return Zend_Db_Select
     */
    public function getSelect()
    {
        return $this->conn_read->select()->from(array('eav_attribute' => $this->eav_attribute_table));
    }
}