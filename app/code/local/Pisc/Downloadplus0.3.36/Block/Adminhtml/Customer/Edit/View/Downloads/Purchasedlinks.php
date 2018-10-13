<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Adminhtml Customer View Downloads Purchased Links block
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Edit_View_Downloads_Purchasedlinks extends Pisc_Downloadplus_Block_Adminhtml_Customer_Edit_Tab_Downloads_Purchasedlinks
{
	
    protected function _prepareColumns()
    {
    	
    	$this->addColumn('order_increment_id', array(
            'header'    => $this->__('Order #'),
            'sortable'  => false,
            'index'     => 'order_increment_id',
    		'type'		=> 'number'
        ));

    	$this->addColumn('product_sku', array(
            'header'    => $this->__('SKU'),
            'sortable'  => false,
            'index'     => 'product_sku',
    		'type'		=> 'text'
        ));

        $this->addColumn('product_title', array(
            'header'    => $this->__('Product'),
            'sortable'  => false,
            'index'     => 'product_name',
        	'type'		=> 'text'
        ));

        $this->addColumn('link_title', array(
            'header'    => $this->__('Download'),
            'sortable'  => false,
            'index'     => 'link_title',
        	'type'		=> 'text'
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'sortable'  => false,
            'index'     => 'status',
        	'type'		=> 'text'
        ));

        $this->addColumn('downloads_used', array(
            'header'    => $this->__('Downloads'),
            'sortable'  => false,
            'index'     => 'number_of_downloads_used',
            'type'      => 'number',
        	'renderer'	=> 'Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Downloads'
        ));

        $this->addColumn('expires_on', array(
            'header'    => $this->__('Expires on'),
            'sortable'  => false,
            'index'     => 'expires_on',
        	'renderer'  => 'Pisc_Downloadplus_Block_Adminhtml_Customer_Renderer_Expires'
        ));
        
        $this->addColumn('date', array(
            'header'    => $this->__('Recently Downloaded on'),
            'sortable'  => false,
            'index'     => 'newest_download_date',
            'type'      => 'text'
        ));
        
    }

}