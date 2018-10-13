<?php

class Krishinc_Fieldtester_Block_Adminhtml_Fieldtester_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
     
      $this->setId('fieldtesterGrid');
      $this->setDefaultSort('fieldtester_id');
      $this->setDefaultDir('ASC');
      
      $this->setSaveParametersInSession(true);
      
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('fieldtester/fieldtester')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('fieldtester_id', array(
          'header'    => Mage::helper('fieldtester')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'fieldtester_id',
      ));

      $this->addColumn('firstname', array(
          'header'    => Mage::helper('fieldtester')->__('First Name'),
          'align'     =>'left',
          'index'     => 'firstname',
      ));     

      $this->addColumn('email', array(
          'header'    => Mage::helper('fieldtester')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
    $this->addColumn('lastname', array(
          'header'    => Mage::helper('fieldtester')->__('Last Name'),
          'align'     =>'left',
          'index'     => 'lastname',
      ));
    $this->addColumn('address1', array(
          'header'    => Mage::helper('fieldtester')->__('Address1'),
          'align'     =>'left',
          'index'     => 'address1',
      ));    
    $this->addColumn('address2', array(
          'header'    => Mage::helper('fieldtester')->__('Address2'),
          'align'     =>'left',
          'index'     => 'address2',
      ));
  
    $this->addColumn('country_id', array(
          'header'    => Mage::helper('fieldtester')->__('Country'),
          'align'     =>'left',
          'index'     => 'country_id',
          'type'	  => 'country'
      ));
	$this->addColumn('zipcode', array(
          'header'    => Mage::helper('fieldtester')->__('Zipcode'),
          'align'     =>'left',
          'index'     => 'zipcode',
      )); 
    $this->addColumn('created_time', array(
          'header'    => Mage::helper('fieldtester')->__('Created Time'),
          'align'     =>'left',
          'index'     => 'created_time',
          'type'	  => 'datetime'
      ));

	  $this->addColumn('update_time', array(
          'header'    => Mage::helper('fieldtester')->__('Update Time'),
          'align'     =>'left',
          'index'     => 'update_time',
          'type'	  => 'datetime'
      ));

	 

    /*  $this->addColumn('status', array(
          'header'    => Mage::helper('fieldtester')->__('Status'),
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
                'header'    =>  Mage::helper('fieldtester')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('fieldtester')->__('View'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('fieldtester')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('fieldtester')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('fieldtester_id');
        $this->getMassactionBlock()->setFormFieldName('fieldtester');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('fieldtester')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('fieldtester')->__('Are you sure?')
        ));

       
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/view', array('id' => $row->getId()));
  }

}