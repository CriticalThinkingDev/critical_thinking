<?php

class Krishinc_Offerlanding_Block_Adminhtml_Offerlanding_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
     
      $this->setId('offerlandingGrid');
      $this->setDefaultSort('offerlanding_id');
      $this->setDefaultDir('ASC');
      
      $this->setSaveParametersInSession(true);
      
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('offerlanding/offerlanding')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('offerlanding_id', array(
          'header'    => Mage::helper('offerlanding')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'offerlanding_id',
      ));

      $this->addColumn('firstname', array(
          'header'    => Mage::helper('offerlanding')->__('First Name'),
          'align'     =>'left',
          'index'     => 'firstname',
      ));
    $this->addColumn('lastname', array(
          'header'    => Mage::helper('offerlanding')->__('Last Name'),
          'align'     =>'left',
          'index'     => 'lastname',
      )); 
      $this->addColumn('email', array(
          'header'    => Mage::helper('offerlanding')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
    $this->addColumn('address1', array(
          'header'    => Mage::helper('offerlanding')->__('Address1'),
          'align'     =>'left',
          'index'     => 'address1',
      ));    
    $this->addColumn('address2', array(
          'header'    => Mage::helper('offerlanding')->__('Address2'),
          'align'     =>'left',
          'index'     => 'address2',
      )); 
   
  
    $this->addColumn('country_id', array(
          'header'    => Mage::helper('offerlanding')->__('Country'),
          'align'     =>'left',
          'index'     => 'country_id',
          'type'	  => 'country'
      ));
      
      	$this->addColumn('zipcode', array(
          'header'    => Mage::helper('offerlanding')->__('Zipcode'),
          'align'     =>'left',
          'index'     => 'zipcode',
      )); 
      
   	$this->addColumn('supply', array(
          'header'    => Mage::helper('offerlanding')->__('Supply'),
          'align'     =>'left',
          'index'     => 'supply',
      ));
    $this->addColumn('created_time', array(
          'header'    => Mage::helper('offerlanding')->__('Created Time'),
          'align'     =>'left',
          'index'     => 'created_time',
          'type'	  => 'datetime'
      ));

	  $this->addColumn('update_time', array(
          'header'    => Mage::helper('offerlanding')->__('Updated Time'),
          'align'     =>'left',
          'index'     => 'update_time',
          'type'	  => 'datetime'
      ));

	 

      /*$this->addColumn('status', array(
          'header'    => Mage::helper('offerlanding')->__('Status'),
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
                'header'    =>  Mage::helper('offerlanding')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('offerlanding')->__('View'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('offerlanding')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('offerlanding')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('offerlanding_id');
        $this->getMassactionBlock()->setFormFieldName('offerlanding');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('offerlanding')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('offerlanding')->__('Are you sure?')
        ));

     
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/view', array('id' => $row->getId()));
  }

}