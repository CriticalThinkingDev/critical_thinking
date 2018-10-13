<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license    Commercial Unlimited License
 */

/**
 * Adminhtml Product Serialnumbers Available Block
 *
 * @category   Pisc
 * @package    Pisc_Downdloadplus
 * @author
 * @version		1.3
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Serialnumbers_Available extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('availableProductSerialnumbers');
        $this->setTemplate('downloadplus/product/edit/serialnumbers/grid.phtml');

        $this->setEmptyText($this->__('There are no entries available at the moment'));
        $this->setSortable(false);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    /*
     * Returns the current Product
     */
    protected function getProduct()
    {
		return Mage::registry('current_product');
    }

    protected function _prepareCollection()
    {
    	$collection = Mage::getModel('downloadplus/product_serialnumber')->getCollection()
    						->addProductToFilter($this->getProduct());
    	$collection->getSelect()->order('created_at DESC');

	    $this->setCollection($collection);
	    return $this;
    }

	/*
	 * Updates the Collection
	 */
	 protected function _afterLoadCollection()
	 {
/*
	  		$items = $this->getCollection()->getItems();
	  	  	foreach ($items as $item) {
	  	  	}
*/
	  	  return $this;
	}

    protected function _prepareColumns()
    {
    	$this->addColumn('serialnumber_remove_id', array(
    		'header'	=> $this->__('Remove'),
    		'sortable'	=> false,
    		'field_name'=> 'downloadplus[serialnumber_remove_id][]',
    		'index'		=> 'serial_hash',
    		'align'     => 'center',
    		'renderer'  => 'Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox'
    	));

    	$this->addColumn('pool', array(
    		'header'    => $this->__('Pool'),
    		'sortable'  => true,
    		'index'     => 'serial_number_pool',
    		'type'		=> 'text',
    		'renderer'  => 'Pisc_Downloadplus_Block_Adminhtml_Renderer_Grid_Column_Serialnumber_Pool'
    	));
    	 
    	$this->addColumn('serialnumber', array(
            'header'    => $this->__('Serialnumber'),
            'sortable'  => true,
            'index'     => 'serial_number',
    		'type'		=> 'text'
        ));

        $this->addColumn('created_at', array(
            'header'    => $this->__('Created on'),
            'sortable'  => true,
            'index'     => 'created_at',
        	'type'		=> 'datetime'
        ));

        return parent::_prepareColumns();
    }

    protected function _preparePage()
    {
    	// Empty to override parent function
    }

    public function getRowUrl($row)
    {
    	return false;
    }

}