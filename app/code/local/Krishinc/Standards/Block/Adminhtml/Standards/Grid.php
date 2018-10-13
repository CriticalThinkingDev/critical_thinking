<?php

class Krishinc_Standards_Block_Adminhtml_Standards_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('standardsGrid');
      $this->setDefaultSort('standards_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('standards/standards')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('standards_id', array(
          'header'    => Mage::helper('standards')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'standards_id',
      ));

	 /*  $this->addColumn('product_id', array(
          'header'    => Mage::helper('standards')->__('Product ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'product_id',
      )); */

	   $this->addColumn('sku', array(
          'header'    => Mage::helper('standards')->__('SKU'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'sku',
      ));

      $this->addColumn('state', array(
          'header'    => Mage::helper('standards')->__('State'),
          'align'     =>'left',
          'index'     => 'state',
      ));

	    $this->addColumn('standard', array(
          'header'    => Mage::helper('standards')->__('Standard'),
          'align'     =>'left',
          'index'     => 'standard',
      ));

	 /* $this->addColumn('benchmark', array(
          'header'    => Mage::helper('standards')->__('Benchmark'),
          'align'     =>'left',
          'index'     => 'benchmark'
      )); */

	  $this->addColumn('page_numbers', array(
          'header'    => Mage::helper('standards')->__('Page Numbers'),
          'align'     =>'left',
          'index'     => 'page_numbers'
      ));


      /* $this->addColumn('status', array(
          'header'    => Mage::helper('standards')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      )); */
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('standards')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('standards')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('standards')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('standards')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('standards_id');
        $this->getMassactionBlock()->setFormFieldName('standards');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('standards')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('standards')->__('Are you sure?')
        ));

        //$statuses = Mage::getSingleton('standards/status')->getOptionArray();

      //  array_unshift($statuses, array('label'=>'', 'value'=>''));
      //  $this->getMassactionBlock()->addItem('status', array(
      //       'label'=> Mage::helper('standards')->__('Change status'),
       //      'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
      //       'additional' => array(
       //             'visibility' => array(
       //                  'name' => 'status',
        //                 'type' => 'select',
        ///                 'class' => 'required-entry',
      ///                   'label' => Mage::helper('standards')->__('Status'),
       //                  'values' => $statuses
         //            )
          //   )
       // ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}