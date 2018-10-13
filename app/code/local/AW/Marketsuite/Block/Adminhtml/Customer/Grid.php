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

class AW_Marketsuite_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    private $tsp_rule = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('mssCustomerGrid');
        //$this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $filter = urldecode(base64_decode($this->getRequest()->getParam('filter')));
        $data = array();
        parse_str($filter, $data);

        if (!empty($data['tsp_rule'])) $this->tsp_rule = $data['tsp_rule'];

        $collection = Mage::getModel('marketsuite/filter')
            ->exportCustomers($this->tsp_rule)
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('gender')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function getResetFilterButtonHtml()
    {
        $collection = Mage::getModel('marketsuite/filter')->getCollection();

        $html =
            '<span class="filter">' .
                Mage::helper('marketsuite')->__('Apply MSS rule: ') .
                '<select id="tsp_rule" name="tsp_rule" style="width:100px">
                    <option value="0">---</option>';

        foreach ($collection as $item)
        {
            if (!$item->getIsActive()) continue;
            if ($this->tsp_rule == $item->getId()) $selected = 'selected=selected';
            else $selected = '';
            $html .= '<option ' . $selected . ' value=' . $item->getId() . '>' . $item->getName() . '</option>';
        }

        $html .= '</select></span>';

        return $html . $this->getChildHtml('reset_filter_button');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'type' => 'number',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('customer')->__('Name'),
            'index' => 'name'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '150',
            'index' => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt' => 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header' => Mage::helper('customer')->__('Group'),
            'width' => '100',
            'index' => 'group_id',
            'type' => 'options',
            'options' => $groups,
        ));

        $this->addColumn('Telephone', array(
            'header' => Mage::helper('customer')->__('Telephone'),
            'width' => '100',
            'index' => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header' => Mage::helper('customer')->__('ZIP'),
            'width' => '90',
            'index' => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header' => Mage::helper('customer')->__('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header' => Mage::helper('customer')->__('State/Province'),
            'width' => '100',
            'index' => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header' => Mage::helper('customer')->__('Customer Since'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true
        ));

        $this->addColumn('gender', array(
            'header' => Mage::helper('customer')->__('Gender'),
            'type' => 'options',
            'align' => 'center',
            'options' => Mage::getModel('marketsuite/source_gender')->toShortOptionArray(),
            'width' => 90,
            'index' => 'gender',
            'filter_condition_callback' => array($this, '_filterGenderCondition')
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('customer')->__('Website'),
                'align' => 'center',
                'width' => '80px',
                'type' => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index' => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header' => Mage::helper('customer')->__('Action'),
                'align' => 'center',
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('customer')->__('View'),
                        'url' => array('base' => '*/*/viewcustomer'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('XML'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }

    protected function _filterGenderCondition($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) return;
        if ($value == AW_Marketsuite_Model_Source_Gender::NOT_SPECIFIED) {
            $collection->addAttributeToFilter('gender', array('null' => true), 'left');
        } else {
            $collection->addAttributeToFilter('gender', array('eq' => $value));
        }
    }
}
