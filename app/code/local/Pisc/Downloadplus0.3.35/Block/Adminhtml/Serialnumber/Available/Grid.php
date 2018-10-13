<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    PISC
 * @package     PISC_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_Adminhtml_Serialnumber_Available_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
    	$collection = Mage::getModel('downloadplus/product_serialnumber')->getCollection()
    						->addGlobalToFilter();

    	$collection->getSelect()->order('created_at DESC');
    	
    	$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
    	$this->addColumn('pool', array(
    		'header'    => $this->__('Pool'),
    		'sortable'  => true,
    		'index'     => 'serial_number_pool',
    		'type'		=> 'text'
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

    protected function _prepareMassaction()
    {
    	$this->setMassactionIdField('serialnumber_id');
    	$this->getMassactionBlock()->setFormFieldName('serialnumber_ids');
    
    	$this->getMassactionBlock()->addItem('remove', array(
    		'label'=> Mage::helper('downloadplus')->__('Remove'),
    		'url'  => $this->getUrl('*/*/massRemove'),
    	));
    
    	return $this;
    }

    public function getRowUrl($row)
    {
      return false;
    }

}
