<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    PISC
 * @package     PISC_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_Adminhtml_Serialnumber_Assigned_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('downloadplus_serialnumberAssignedGrid');
        $this->setUseAjax(false);
        $this->setDefaultSort('order_increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
    	$helper = Mage::helper('downloadplus/eav');

    	$collection = Mage::getResourceModel('downloadplus/link_purchased_item_serialnumber_collection');
        $collection->addOrderItemToResult(Array('order_id'=>'order_id', 'sku'=>'sku', 'name'=>'name'));
        $collection->addOrderToResult(Array('increment_id'=>'increment_id'));

        foreach ($collection as $item) {
			if ($order = Mage::getModel('sales/order')->loadByIncrementId($item->getIncrementId(), 'increment_id')) {
				$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
				$item->setCustomerName($customer->getName());
				$item->setCustomerEmail($customer->getEmail());
			}
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('order_increment_id', array(
            'header'=> Mage::helper('downloadplus')->__('Order #'),
//            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));
        
        $this->addColumn('customer_name', array(
        	'header'=> Mage::helper('downloadplus')->__('Customer Name'),
        	'type'  => 'text',
        	'index' => 'customer_name',
        	'filter'=> false,
        ));

        $this->addColumn('customer_email', array(
        		'header'=> Mage::helper('downloadplus')->__('Customer Email'),
        		'type'  => 'text',
        		'index' => 'customer_email',
        		'filter'=> false,
        ));
        
        $this->addColumn('product_sku', array(
            'header'=> Mage::helper('downloadplus')->__('SKU'),
//            'width' => '80px',
            'type'  => 'text',
            'index' => 'sku',
        ));

        $this->addColumn('product_name', array(
            'header'=> Mage::helper('downloadplus')->__('Product'),
//            'width' => '80px',
            'type'  => 'text',
            'index' => 'name',
        ));

        $this->addColumn('serial_title', array(
            'header'=> Mage::helper('downloadplus')->__('Download Title'),
//            'width' => '80px',
            'type'  => 'text',
            'index' => 'serial_title',
        ));

        $this->addColumn('serial_number', array(
            'header'=> Mage::helper('downloadplus')->__('Serial #'),
//            'width' => '80px',
            'type'  => 'text',
            'index' => 'serial_number',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
          if ($row->getOrderId()) {
            $params = array('order_id'=>$row->getOrderId());
            if ($this->getRequest()->getParam('store')) {
                $params['store'] = $this->getRequest()->getParam('store');
            }
            $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/view', $params);
            return $url;
          }
        }
        return false;
    }

}
