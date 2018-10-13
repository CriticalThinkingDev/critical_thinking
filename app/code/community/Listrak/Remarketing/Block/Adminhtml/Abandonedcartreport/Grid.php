<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.0.0
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2011 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

class Listrak_Remarketing_Block_Adminhtml_Abandonedcartreport_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('abandonedCartsGrid');
        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $reportTimeout = Mage::getStoreConfig('remarketing/abandonedcarts/abandoned_cart_report_timeout') * 60;

        $startDate = gmdate('Y-m-d H:i:s', time() - $reportTimeout);
        $collection = Mage::getModel('listrak/abandonedcart')->getCollection()
            ->addFieldToFilter('main_table.updated_at', array('lt' => $startDate))
            ->setPrepareForReport(true);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'is_customer',
            array(
                'header' => Mage::helper('remarketing')->__('Registered'),
                'index' => 'is_customer',
                'width' => '75px',
                'sortable' => false,
                'filter' => false
            )
        );

        $this->addColumn(
            'session_id',
            array(
                'header' => Mage::helper('remarketing')->__('Session Id'),
                'index' => 'session_id',
                'width' => '250px',
                'sortable' => false,
                'filter' => false
            )
        );

        $this->addColumn(
            'customer_name',
            array(
                'header' => Mage::helper('remarketing')->__('Customer Name'),
                'index' => 'customer_name',
                'sortable' => false,
                'filter' => false
            )
        );

        $this->addColumn(
            'email',
            array(
                'header' => Mage::helper('remarketing')->__('Email'),
                'index' => 'email',
                'sortable' => false,
                'filter' => false
            )
        );

        $this->addColumn(
            'items_count',
            array(
                'header' => Mage::helper('remarketing')->__('# Items'),
                'width' => '80px',
                'align' => 'right',
                'index' => 'items_qty',
                'sortable' => false,
                'type' => 'number',
                'filter' => false
            )
        );

        $this->addColumn(
            'total',
            array(
                'header' => Mage::helper('remarketing')->__('Total'),
                'width' => '80px',
                'type' => 'currency',
                'currency_code' => (string)Mage::getStoreConfig(
                    Mage_Directory_Model_Currency::XML_PATH_CURRENCY_DEFAULT
                ),
                'index' => 'grand_total',
                'sortable' => false,
                'renderer' => 'adminhtml/report_grid_column_renderer_currency',
                'filter' => false
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('remarketing')->__('Creation Date'),
                'width' => '170px',
                'type' => 'datetime',
                'index' => 'created_at',
                //'filter_index'=>'main_table.created_at',
                'sortable' => false,
                'filter' => false
            )
        );

        $this->addColumn(
            'updated_at',
            array(
                'header' => Mage::helper('remarketing')->__('Abandon Date'),
                'width' => '170px',
                'type' => 'datetime',
                'index' => 'updated_at',
                //'filter_index'=>'main_table.updated_at',
                'sortable' => false,
                'filter' => false
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }


}