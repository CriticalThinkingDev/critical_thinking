<?php

class Krishinc_Free_Block_Adminhtml_Free_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('freeGrid');
      $this->setDefaultSort('donation_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('free/free')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('donation_id', array(
          'header'    => Mage::helper('free')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'donation_id',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('free')->__('Full Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));

	$this->addColumn('access_code', array(
          'header'    => Mage::helper('free')->__('Access Code'),
          'align'     =>'left',
          'index'     => 'access_code',
      ));
	
	$this->addColumn('donation_at', array(
          'header'    => Mage::helper('free')->__('Donate Date'),
          'align'     =>'left',
	  'type'      => 'datetime',
          'index'     => 'donation_at',
      ));

	  
		$this->addExportType('*/*/exportCsv', Mage::helper('free')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('free')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    

  

}
