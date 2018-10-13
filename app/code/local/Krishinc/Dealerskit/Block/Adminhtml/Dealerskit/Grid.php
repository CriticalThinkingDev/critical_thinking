<?php

class Krishinc_Dealerskit_Block_Adminhtml_Dealerskit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
     
      $this->setId('dealerskitGrid');
      $this->setDefaultSort('dealerskit_id');
      $this->setDefaultDir('ASC');
      
      $this->setSaveParametersInSession(true);
      
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('dealerskit/dealerskit')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('dealerskit_id', array(
          'header'    => Mage::helper('dealerskit')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'dealerskit_id',
      ));
	$this->addColumn('company', array(
	  'header'    => Mage::helper('dealerskit')->__('Company Name'),
	  'align'     =>'left',
	  'index'     => 'company',
	));
	$this->addColumn('attention', array(
	      'header'    => Mage::helper('dealerskit')->__('Attention'),
	      'align'     =>'left',
	      'index'     => 'attention',
	  ));    
	$this->addColumn('email', array(
	  'header'    => Mage::helper('dealerskit')->__('Email'),
	  'align'     =>'left',
	  'index'     => 'email',
	)); 
  	$this->addColumn('address1', array(
          'header'    => Mage::helper('dealerskit')->__('Address1'),
          'align'     =>'left',
          'index'     => 'address1',
      ));  
	$this->addColumn('address2', array(
		'header'    => Mage::helper('dealerskit')->__('Address2'),
		'align'     =>'left',
		'index'     => 'address2',
	)); 
	$this->addColumn('country_id', array(
		'header'    => Mage::helper('dealerskit')->__('Country'),
		'align'     =>'left',
		'index'     => 'country_id',
		'type'      =>'country',
	));
	$this->addColumn('region', array(
          'header'    => Mage::helper('dealerskit')->__('Region'),
          'align'     =>'left',
          'index'     => 'region',
	      'type'      =>'region',
	      'renderer'  => 'dealerskit/renderer_region',
      ));
  	   $this->addColumn('zipcode', array(
          'header'    => Mage::helper('dealerskit')->__('Zipcode'),
          'align'     =>'left',
          'index'     => 'zipcode',
      ));  
   
	 $this->addColumn('created_time', array(
          'header'    => Mage::helper('dealerskit')->__('Created Time'),
          'align'     =>'left',
          'index'     => 'created_time',
          'type'	  => 'datetime'
      ));

 	$this->addColumn('update_time', array(
          'header'    => Mage::helper('dealerskit')->__('Updated Time'),
          'align'     =>'left',
          'index'     => 'update_time',
          'type'	  => 'datetime'
      ));
 
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('dealerskit')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('dealerskit')->__('View'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('dealerskit')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('dealerskit')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('dealerskit_id');
        $this->getMassactionBlock()->setFormFieldName('dealerskit');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('dealerskit')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('dealerskit')->__('Are you sure?')
        ));

        
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/view', array('id' => $row->getId()));
  }

}