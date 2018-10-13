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

abstract class AW_Marketsuite_Model_Product_Abstract extends AW_Marketsuite_Model_Object
{
    protected static $instance;
    protected static $_attributesCache;

    /**
     * Getting instance of product model according to magento version
     * @return mixed 
     */
    public static function getInstance()
    {
        if(!self::$instance) self::$instance = Mage::getModel('marketsuite/product_mag13');
        return clone self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        $this->select = $this->conn_read->select()->from(array('product_table' => $this->resource->getTableName('catalog/product')));
    }

    public function getData($key = '', $index = null) {
        if (!parent::getData('entity_id')) return null;
        if (!parent::getData($key, $index))
            $this->setData($key, $this->addAdditionalAttribute($key));

        return parent::getData($key, $index);
    }

    /**
     * Getting product by Id
     * @param int $productId
     * @return AW_Marketsuite_Model_Product_Abstract
     */
    public function load($productId)
    {
        if (!isset(self::$selectCache['product_select']))
        {
            self::$selectCache['product_select'] =
                $this->getSelect()->where('product_table.entity_id = ?', 'product_id_replace')
                    ->assemble();
        }
        $select = str_replace('product_id_replace', $productId, self::$selectCache['product_select']);

        $product = $this->conn_read->fetchRow($select);
        if ($product) $this->addData($product);
        return $this;
    }

    public function addAdditionalAttribute($attributeCode)
    {
        $storeId = Mage::app()->getStore()->getId() ? Mage::app()->getStore()->getId() : 0;
        
        if (!isset(self::$_attributesCache[$attributeCode])) {
            $attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attributeCode);
            if (!$attribute){
		if($attributeCode == 'category'){
                    $productModel = Mage::getModel('catalog/product');
                    $productModel->load($this->getData('entity_id'));
                    return $productModel->getCategoryIds();
                }else{
                    return;
                }
            }
            self::$_attributesCache[$attributeCode] = array(
                'entity_type_id'    => $attribute->getEntityTypeId(),
                'attribute_id'      => $attribute->getId(),
                'table'             => $attribute->getBackend()->getTable(),
                'is_global'         => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'backend_type'      => $attribute->getBackendType()
            );
        }
        
        $attribute = self::$_attributesCache[$attributeCode];

        if ($attribute['is_global'])
        {
            if (!isset(self::$selectCache['add_additional_attribute_global']))
            {
                $select = $this->getSelect()->where('product_table.entity_id = ?', 'product_id_replace');
                $select->joinLeft(
                    array('t1_attribute_code_replace' => 'attribute_table_replace'),
                    'product_table.entity_id=t1_attribute_code_replace.entity_id
                        AND t1_attribute_code_replace.store_id=0
                            AND t1_attribute_code_replace.attribute_id=attribute_id_replace',
                    array()
                );
                $select->columns(array('attribute_code_replace' => 't1_attribute_code_replace.value'));
                self::$selectCache['add_additional_attribute_global'] = $select->assemble();
            }
            $search = array('attribute_code_replace', 'attribute_table_replace', 'attribute_id_replace', 'product_id_replace');
            $replace = array($attributeCode, $attribute['table'], $attribute['attribute_id'], $this->getEntityId());
            $select = str_replace($search, $replace, self::$selectCache['add_additional_attribute_global']);
        }
        else
        {
            if (!isset(self::$_attributesCache['add_additional_attribute']))
            {
                $select = $this->getSelect()->where('product_table.entity_id = ?', 'product_id_replace');
                $select->joinLeft(
                    array('t1_attribute_code_replace' => 'attribute_table_replace'),
                    'product_table.entity_id=t1_attribute_code_replace.entity_id
                        AND t1_attribute_code_replace.store_id=0
                            AND t1_attribute_code_replace.attribute_id=attribute_id_replace',
                    array()
                );
                $select->joinLeft(
                    array('t2_attribute_code_replace' => 'attribute_table_replace'),
                    't1_attribute_code_replace.entity_id = t2_attribute_code_replace.entity_id
                        AND t1_attribute_code_replace.attribute_id = t2_attribute_code_replace.attribute_id
                        AND t2_attribute_code_replace.store_id=store_id_replace',
                    array()
                );
                $select->columns(array('attribute_code_replace' => 'IFNULL(t2_attribute_code_replace.value, t1_attribute_code_replace.value)'));
                self::$selectCache['add_additional_attribute'] = $select->assemble();
            }
            $search = array('attribute_code_replace', 'attribute_table_replace', 'attribute_id_replace', 'store_id_replace', 'product_id_replace');
            $replace = array($attributeCode, $attribute['table'], $attribute['attribute_id'], $storeId, $this->getEntityId());
            $select = str_replace($search, $replace, self::$selectCache['add_additional_attribute']);
        }

        try
        { $result = $this->conn_read->fetchRow($select); }
        catch (Exception $ex)
        { return; }
        if (isset($result[$attributeCode])) return $result[$attributeCode];
    }
}