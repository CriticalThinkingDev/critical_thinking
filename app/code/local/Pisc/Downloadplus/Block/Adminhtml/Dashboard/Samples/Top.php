<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Adminhtml dashboard top downloads block
 *
 * @category   Pisc
 * @package    Pisc_Downdloadplus
 * @author
 */

class Pisc_Downloadplus_Block_Adminhtml_Dashboard_Samples_Top extends Mage_Adminhtml_Block_Dashboard_Grid
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('topDownloadSamplesGrid');
    }

    protected function _prepareCollection()
    {
	      $collection = Mage::getModel('downloadplus/log')->getCollection()
	      	->getTopDownloadSamples();

	      $this->setCollection($collection);
	      return parent::_prepareCollection();
    }

	 /*
	  * Updates the Collection with EAV Data
	  */
	  protected function _afterLoadCollection()
	  {
	  	  $items = $this->getCollection()->getItems();
	  	  foreach ($items as $item) {
	  	  		if ($item->getData('product_id')!='' && $item->getData('product_sku')=='') {
	  	  	  		$product = Mage::getModel('catalog/product');
	  	  	  		$product->load($item->getData('product_id'));
	  	  	  		$item->setData('product_sku', $product->getSku());
	  	  	  		$item->setData('product_name', $product->getName());
	  	  		}
	  	  		if ($item->getData('product_sku')!='' && $item->getData('product_id')=='') {
	  	  	  		$product = Mage::getModel('catalog/product');
	  	  	  		$item->setData('product_id', $product->getIdBySku($item->getData('product_sku')));
	  	  		}
	  	  }

	  	  return $this;
	  }

    protected function _prepareColumns()
    {
    	$this->addColumn('product_name', array(
            'header'    => $this->__('Product Name'),
            'sortable'  => false,
            'index'     => 'product_name'
        ));

        $this->addColumn('title', array(
            'header'    => $this->__('Download Title'),
            'sortable'  => false,
            'index'     => 'title'
        ));

        $this->addColumn('total', array(
            'header'    => $this->__('Downloads'),
            'sortable'  => false,
            'index'     => 'total',
            'type'      => 'number'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
    	if ($row->getProductId()) {
	        $params = array('id'=>$row->getProductId());
	        if ($this->getRequest()->getParam('store')) {
	            $params['store'] = $this->getRequest()->getParam('store');
	        }
	        return $this->getUrl('adminhtml/catalog_product/edit', $params);
    	}
    	return false;
    }

}