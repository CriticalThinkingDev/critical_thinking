<?php

class Krishinc_Catalogrequest_Block_Adminhtml_Catalogrequest_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
     
      $this->setId('catalogrequestGrid');
      $this->setDefaultSort('catalogrequest_id');
      $this->setDefaultDir('ASC');
      
      $this->setSaveParametersInSession(true);
      
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('catalogrequest/catalogrequest')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('catalogrequest_id', array(
          'header'    => Mage::helper('catalogrequest')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'catalogrequest_id',
      ));

      $this->addColumn('firstname', array(
          'header'    => Mage::helper('catalogrequest')->__('First Name'),
          'align'     =>'left',
          'index'     => 'firstname',
      ));
    $this->addColumn('lastname', array(
          'header'    => Mage::helper('catalogrequest')->__('Last Name'),
          'align'     =>'left',
          'index'     => 'lastname',
      ));
    $this->addColumn('email', array(
          'header'    => Mage::helper('catalogrequest')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      )); 
      
      $this->addColumn('schoolname', array(
          'header'    => Mage::helper('catalogrequest')->__('School Name'),
          'align'     =>'left',
          'index'     => 'schoolname',
      ));
    $this->addColumn('mailing_address', array(
          'header'    => Mage::helper('catalogrequest')->__('Mailing Address'),
          'align'     =>'left',
          'index'     => 'mailing_address',
      ));  
        $allmarket = Mage::helper('catalogrequest')->getAllMarket();
      $this->addColumn('market',array(
      	  'header'    => Mage::helper('catalogrequest')->__('Market'),
          'align'     =>'left',
          'index'     => 'market',
	      'type'      =>'options',
	      'options'		=> $allmarket
      ));
    $this->addColumn('country_id', array(
          'header'    => Mage::helper('catalogrequest')->__('Country'),
          'align'     =>'left',
          'index'     => 'country_id',
	      'type'      =>'country',
      ));
    $this->addColumn('zipcode', array(
          'header'    => Mage::helper('catalogrequest')->__('Zipcode'),
          'align'     =>'left',
          'index'     => 'zipcode',
      )); 
	 $this->addColumn('created_time', array(
          'header'    => Mage::helper('catalogrequest')->__('Created Time'),
          'align'     =>'left',
          'index'     => 'created_time',
          'type'	  => 'datetime'
      ));

      
 	$this->addColumn('update_time', array(
          'header'    => Mage::helper('catalogrequest')->__('Updated Time'),
          'align'     =>'left',
          'index'     => 'update_time',
          'type'	  => 'datetime'
      ));
  
      
	  
      $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('catalogrequest')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('catalogrequest')->__('View'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('catalogrequest')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('catalogrequest')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('catalogrequest_id');
        $this->getMassactionBlock()->setFormFieldName('catalogrequest');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('catalogrequest')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('catalogrequest')->__('Are you sure?')
        ));

        
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/view', array('id' => $row->getId()));
  }

} 