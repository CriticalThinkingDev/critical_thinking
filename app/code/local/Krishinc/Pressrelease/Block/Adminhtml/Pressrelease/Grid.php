<?php

class Krishinc_Pressrelease_Block_Adminhtml_Pressrelease_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('pressreleaseGrid');
      $this->setDefaultSort('pressrelease_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('pressrelease/pressrelease')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('pressrelease_id', array(
          'header'    => Mage::helper('pressrelease')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'pressrelease_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('pressrelease')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	   $this->addColumn('dateline', array(
          'header'    => Mage::helper('pressrelease')->__('Dateline'),
          'align'     =>'left',
          'index'     => 'dateline',
      ));

	  $this->addColumn('pressdate', array(
          'header'    => Mage::helper('pressrelease')->__('Pressdate'),
          'align'     =>'left',
          'index'     => 'pressdate',
          'type'      => 'date',
      ));


    /*  $this->addColumn('status', array(
          'header'    => Mage::helper('pressrelease')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));*/
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('pressrelease')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('pressrelease')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('pressrelease')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('pressrelease')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('pressrelease_id');
        $this->getMassactionBlock()->setFormFieldName('pressrelease');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('pressrelease')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('pressrelease')->__('Are you sure?')
        ));

//        $statuses = Mage::getSingleton('pressrelease/status')->getOptionArray();
//
//        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('pressrelease')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'status',
//                         'type' => 'select',
//                         'class' => 'required-entry',
//                         'label' => Mage::helper('pressrelease')->__('Status'),
//                         'values' => $statuses
//                     )
//             )
//        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}