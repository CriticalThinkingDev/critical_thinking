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

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Detail_Availablesamples extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('availableDownloadableSampleFilesGrid');
        $this->setTemplate('downloadplus/product/edit/detail/grid.phtml');

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
    	// Filter out currently assigned files
		$currentFiles = Mage::getModel('downloadplus/download_detail')
							->getCollection()
							->getRelatedFiles();

    	$collection = Mage::getModel('downloadplus/download_detail')->getCollection()
    						->addFilesToFilter($currentFiles)
    						->getAvailablePhysicalSampleFiles();

	    $this->setCollection($collection);
	    //return parent::_prepareCollection();
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
    	$this->addColumn('add', array(
            'sortable'  => false,
            'type'     	=> 'checkbox',
    		'field_name'=> 'downloadplus[add_historical_files][]',
    		'index'		=> 'type',
    		'value'		=> false
        ));

    	$this->addColumn('filename', array(
            'header'    => $this->__('Filename'),
            'sortable'  => false,
            'index'     => 'filename',
    		'type'		=> 'text'
        ));

        $this->addColumn('title', array(
            'header'    => $this->__('Size'),
            'sortable'  => false,
            'index'     => 'size_formatted',
        	'type'		=> 'text'
        ));

        $this->addColumn('date', array(
            'header'    => $this->__('Date'),
            'sortable'  => false,
            'index'     => 'timestamp',
            'type'      => 'datetime'
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