<?php

class Krishinc_Educent_Block_Adminhtml_Educent_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('educentGrid');
      $this->setDefaultSort('educent_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('educent/educent')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('educent_id', array(
          'header'    => Mage::helper('educent')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'educent_id',
      ));

      $this->addColumn('order_id', array(
          'header'    => Mage::helper('educent')->__('Order Id#'),
          'align'     =>'left',
          'index'     => 'order_id',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('educent')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('response', array(
          'header'    => Mage::helper('educent')->__('Track Response'),
          'align'     =>'left',
          'index'     => 'response',
      ));

      $this->addColumn('track', array(
          'header'    => Mage::helper('educent')->__('Track Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'track',
          'type'      => 'options',
          'options'   => array(
              0 => 'Not Sent',
              1 => 'Sent',
          ),
      ));
	  

		
		$this->addExportType('*/*/exportCsv', Mage::helper('educent')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('educent')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    



}
