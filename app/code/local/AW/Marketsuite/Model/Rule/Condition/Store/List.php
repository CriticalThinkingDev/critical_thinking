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


class AW_Marketsuite_Model_Rule_Condition_Store_List extends Mage_Rule_Model_Condition_Abstract {

    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_productResouce = null;

    public function __construct() {

        parent::__construct();
        $this->setType('marketsuite/rule_condition_store_list')
                ->setValue(null);
    }

    public function validate($object) {
         
        if ($object instanceof AW_Marketsuite_Model_Order_Abstract) {

            $object->setStoreId($object->getCustomer()->getStoreId());
        }

        return parent::validate($object);
    }

    public function loadAttributeOptions() {

        $hlp = Mage::helper('marketsuite');
        $this->setAttributeOption(
                array('store_id' => $hlp->__('Store customer registered at '))
        );

        return $this;
    }

    public function loadOperatorOptions() {
        parent::loadOperatorOptions();

        $operatorbytype = $this->getOperatorByInputType();

        $operatorbytype['multiselect'] = array('()', '!()');

        $this->setOperatorByInputType($operatorbytype);

        return $this;
    }

    public function getAttributeElement() {

        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getValueElementType() {

        if ($this->getAttribute() == 'store_id') {

            return 'multiselect';
        }

        return parent::getValueElementType();
    }

    public function getValueSelectOptions() {

        if ($this->getAttribute() == 'store_id') {

            return Mage::helper('marketsuite')->getStoresArray();
        }

        return parent::getValueSelectOptions();
    }

    public function getDefaultOperatorInputByType() {

        $inputByType = parent::getDefaultOperatorInputByType();

        $inputByType['multiselect'] = array('()', '!()');

        return $inputByType;
    }

    public function getInputType() {

        switch ($this->getAttribute()) {

            case 'store_id':
                return 'multiselect';
        }

        return parent::getInputType();
    }

}