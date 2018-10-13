<?php
/**
 * Customer Edit Downloads Tab Admin block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.3 - MAGENTO 1.4.1.x
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Edit_Tab_Downloads_Additional extends Mage_Adminhtml_Block_Template
{

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('downloadplus/customer/edit/downloads/additional.phtml');
		
		// Load dependencies
		Mage::getModel('downloadable/link');
		Mage::getModel('downloadplus/customer_download');
		Mage::getModel('sales/order');
		Mage::getModel('catalog/product_visibility');
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
				                'id' => 'add_link_item',
				                'class' => 'add',
						));
		return $addButton->toHtml();
	}

	/**
	 * Retrieve default links title
	 */
	public function getLinksTitle()
	{
		return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
	}

	/**
	 * Check exists defined links title
	 */
	public function getUsedDefault()
	{
		return false;
	}

	/**
	 * Return array of links
	 */
	public function getLinkData()
	{
		$linkArr = array();
		$orderItemsCollection = $this->getOrderItemsCollection();

		foreach ($orderItemsCollection as $orderItem) {
			$download = Mage::getModel('downloadplus/link_customer_item');
			$collection = $download->getCollection()->getByOrderItemId($orderItem->getId());
			foreach ($collection as $item) {
				$tmpLinkItem = Array(
					'order_item_id' => $item->getOrderItemId(),
					'link_id' => $item->getId(),
					'title' => $item->getLinkTitle(),
					'description' => $item->getDownloadDetail()->getDetail(),
					'number_of_downloads' => $item->getNumberOfDownloadsBought(),
					'downloads_used' => $item->getNumberOfDownloadsUsed().' '.$this->__('used'),
					'is_unlimited' => '',
					'is_shareable' => $item->getIsShareable(),
					'link_status' => $item->getStatus(),
					'link_url' => $item->getLinkUrl(),
					'link_type' => $item->getLinkType()
				);

				$file = Mage::helper('downloadable/file')->getFilePath(Pisc_Downloadplus_Model_Customer_Download::getBasePath(), $item->getLinkFile());
				if ($item->getLinkFile() && is_file($file)) {
					$name = '<a href="' . $this->getUrl('adminhtml/customer_file/link', array('id' => $item->getId(), '_secure' => true)) . '">' .
							Mage::helper('downloadable/file')->getFileFromPathFile($item->getLinkFile()) .
							'</a>';

					$tmpLinkItem['file_save'] = Array(Array(
													 'file' => $item->getLinkFile(),
													 'name' => $name,
													 'size' => filesize($file),
													 'status' => 'old'
													 ));
				 }

				 if ($item->getNumberOfDownloadsBought() == '0') {
				 	$tmpLinkItem['is_unlimited'] = ' checked="checked"';
				 }

				 $linkArr[] = new Varien_Object($tmpLinkItem);
			}
		}

		return $linkArr;
	}

	/**
	 * Retrieve max downloads value from config
	 */
	public function getConfigMaxDownloads()
	{
		return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_DEFAULT_DOWNLOADS_NUMBER);
	}

	/**
	 * Prepare block Layout
	 */
	protected function _prepareLayout()
	{
		$this->setChild(
            'upload_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
			->addData(array(
	                    'id'      => '',
	                    'label'   => Mage::helper('adminhtml')->__('Upload Files'),
	                    'type'    => 'button',
	                    'onclick' => 'Downloadable.massUploadByType(\'links\')'
	                    ))
                    );
	}

	/**
	 * Retrieve Upload button HTML
	 */
	public function getUploadButtonHtml()
	{
		return $this->getChild('upload_button')->toHtml();
	}

	/**
	 * Retrive config json
	 */
	public function getConfigJson($type='links')
	{
		$this->getConfig()->setUrl(Mage::getModel('adminhtml/url')
									->addSessionParam()
									->getUrl('downloadplusadmin/customer_file/upload',
										Array(
											'type' => $type,
											'customer_id' => $this->getCustomer()->getId(),
										'_secure' => true)));
		$this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
		$this->getConfig()->setFileField($type);
		$this->getConfig()->setFilters(array(
            'all'    => array(
		                'label' => Mage::helper('adminhtml')->__('All Files'),
		                'files' => array('*.*')
						)
		));
		$this->getConfig()->setReplaceBrowseWithRemove(true);
		$this->getConfig()->setWidth('32');
		$this->getConfig()->setHideUploadButton(true);
		return Zend_Json::encode($this->getConfig()->getData());
	}

	/**
	 * Retrive config object
	 */
	public function getConfig()
	{
		if(is_null($this->_config)) {
			$this->_config = new Varien_Object();
		}

		return $this->_config;
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
