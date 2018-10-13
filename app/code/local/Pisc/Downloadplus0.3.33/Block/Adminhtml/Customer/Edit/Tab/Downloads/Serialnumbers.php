<?php
/**
 * Customer Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.3 - MAGENTO 1.4.1.x
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Edit_Tab_Downloads_Serialnumbers extends Mage_Adminhtml_Block_Template
{

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('downloadplus/customer/edit/downloads/serialnumbers.phtml');
		
		// Load dependencies
		Mage::getModel('sales/order');
		Mage::getModel('catalog/product_visibility');
		Mage::getModel('downloadable/product_type');
	}

    /*
     * Returns the current Customer
     */
    protected function getCustomer()
    {
		return Mage::registry('current_customer');
    }

	/**
	 * Retrieve Add button HTML
	 */
	public function getAddButtonHtml()
	{
		$addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
						->setData(array(
				                'label' => Mage::helper('downloadable')->__('Add New Row'),
				                'id' => 'add_serial_item',
				                'class' => 'add',
						));
		return $addButton->toHtml();
	}

	/**
	 * Return array of serial items
	 */
	public function getSerialnumberData()
	{
		$linkArr = array();
		$orderItemsCollection = $this->getOrderItemsCollection();

		foreach ($orderItemsCollection as $orderItem) {
			$serialnumber = Mage::getModel('downloadplus/link_purchased_item_serialnumber');
			$collection = $serialnumber->getCollection()->getByOrderItemId($orderItem->getId());
			foreach ($collection as $item) {
				$tmpSerialItem = Array(
					'order_item_id' => $item->getOrderItemId(),
					'serial_id' => $item->getId(),
					'title' => $item->getSerialTitle(),
					'number' => $item->getSerialNumber(),
					'serial_status' => $item->getStatus()
				);
				$linkArr[] = new Varien_Object($tmpSerialItem);
			}
		}

		return $linkArr;
	}

	/**
	 * Prepare block Layout
	 */
	protected function _prepareLayout()
	{
	}

	/*
	 * Get collection of complete Order Items of this customer
	 */
	public function getOrderItemsCollection()
	{
		$result = Array();

		// We will retrieve only order items for orders with these states
		$order_states = array(
			Mage_Sales_Model_Order::STATE_NEW,
			Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
			Mage_Sales_Model_Order::STATE_PROCESSING,
			Mage_Sales_Model_Order::STATE_HOLDED,
			Mage_Sales_Model_Order::STATE_COMPLETE
		);
		$orderItemsCollection = Mage::getModel('sales/order_item')->getCollection();
		$orderItemsCollection->getSelect()
							->joinLeft(Array('orders' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
										'`main_table`.`order_id`=`orders`.`entity_id`',
										Array('order_increment_id'=>'increment_id'))
							->where('state IN (\''.implode("','",$order_states).'\')')
							->where('customer_id=?', $this->getCustomer()->getId())
							//->where('product_type=?', Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)
		;

		return $orderItemsCollection;
	}

	/*
	 * Get Orderdata of complete Order Items of this customer
	 */
	public function getOrderItems()
	{
		$result = Array();
		$orderItemsCollection = $this->getOrderItemsCollection();

		foreach($orderItemsCollection as $orderItem){
			$result[$orderItem->getItemId()] = Array(
					'order_item_id' => $orderItem->getID(),
					'order_increment_id' => $orderItem->getOrderIncrementId(),
					'sku' => $orderItem->getSku(),
					'name' => $orderItem->getName(),
					'created_at' => $orderItem->getCreatedAt()
				);
		}

		return $result;
	}

	/*
	 * Get Array of all visible Products with SKU=>Product Name
	 */
	public function getProducts()
	{
		$result = Array();

		$storeId = Mage::app()->getStore()->getId();
		$visibility = array(
		Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
		Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
		);
		$collection = Mage::getModel('catalog/product')
						->setStoreId($storeId)
						->getCollection()
						->addAttributeToFilter('visibility', $visibility)
						->addAttributeToSort('name', 'asc');

		foreach ($collection as $entry) {
			$result[$entry->getSku()] = $entry->getName();
		}

		return $result;
	}

}
