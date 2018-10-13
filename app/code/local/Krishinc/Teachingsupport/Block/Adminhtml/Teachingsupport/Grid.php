<?php

class Krishinc_Teachingsupport_Block_Adminhtml_Teachingsupport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('teachingsupportGrid');
      $this->setDefaultSort('teachingsupport_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('teachingsupport/teachingsupport')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('teachingsupport_id', array(
          'header'    => Mage::helper('teachingsupport')->__('ID'),
          'align'     =>'right',
          'width'     => '10px',
          'index'     => 'teachingsupport_id',
      ));
      
  	$this->addColumn('sku', array(
          'header'    => Mage::helper('teachingsupport')->__('SKU'),
          'align'     =>'left',
          'width'     => '50px',
          'index'     => 'sku',
      ));
   	$this->addColumn('title', array(
          'header'    => Mage::helper('teachingsupport')->__('Title'),
          'align'     =>'left',
          'width'     => '150px',
          'index'     => 'title',
      ));
	$this->addColumn('type', array(
          'header'    => Mage::helper('teachingsupport')->__('Type'),
          'align'     =>'left',
          'width'     => '150px',
          'index'     => 'type',
      ));
   	$this->addColumn('pdf', array(
      'header'    => Mage::helper('teachingsupport')->__('Pdf 1'),
      'align'     =>'left',
      'width'     => '50px',
      'index'     => 'pdf',
    ));
   	$this->addColumn('pdf_title1', array(
      'header'    => Mage::helper('teachingsupport')->__('Pdf Title 1'),
      'align'     =>'left',
      'width'     => '50px',
      'index'     => 'pdf_title1',
    ));
	  
     $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('teachingsupport')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('teachingsupport')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('teachingsupport')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('teachingsupport')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('teachingsupport_id');
        $this->getMassactionBlock()->setFormFieldName('teachingsupport');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('teachingsupport')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('teachingsupport')->__('Are you sure?')
        ));

        
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}