<?php

class Krishinc_Catalogrequest_Block_Adminhtml_Catalogrequest_Customgrid extends Krishinc_Catalogrequest_Block_Adminhtml_Widget_Grid
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
      $collection = Mage::getModel('catalogrequest/catalogrequest')->getCollection()->addFieldToFilter('is_export','No')->setOrder('catalogrequest_id','desc');
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
 
      $this->addColumn('firstname', array(
          'header'    => Mage::helper('catalogrequest')->__('First Name'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'firstname',
      ));
    $this->addColumn('lastname', array(
          'header'    => Mage::helper('catalogrequest')->__('Last Name'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'lastname',
      ));    
  	 $this->addColumn('schoolname', array(
          'header'    => Mage::helper('catalogrequest')->__('School Name'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'schoolname',
      ));
        $this->addColumn('mailing_address', array(
          'header'    => Mage::helper('catalogrequest')->__('Mailing Address'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'mailing_address',
      ));  
       
  	  $this->addColumn('appt_unit', array(
          'header'    => Mage::helper('catalogrequest')->__('Appt. Unit'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'appt_unit',
      )); 
         $this->addColumn('city', array(
          'header'    => Mage::helper('catalogrequest')->__('City'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'city',
      ));  
   $this->addColumn('country_id', array(
          'header'    => Mage::helper('catalogrequest')->__('Country'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'country_id', 
	      'type'      =>'country',
	      'renderer'  => 'catalogrequest/renderer_country',
      ));
      $this->addColumn('region', array(
          'header'    => Mage::helper('catalogrequest')->__('Region'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'region',
	      'type'      =>'region',
	      'renderer'  => 'catalogrequest/renderer_region',
      ));
  	 
         $this->addColumn('zipcode', array(
          'header'    => Mage::helper('catalogrequest')->__('Zipcode'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'zipcode',
      ));  
   
       $this->addColumn('email', array(
          'header'    => Mage::helper('catalogrequest')->__('Email'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'email',
      )); 
      
      
     $this->addColumn('phone', array(
          'header'    => Mage::helper('catalogrequest')->__('Telephone'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'phone',
      ));  
   
   	 $this->addColumn('created_time', array(
          'header'    => Mage::helper('catalogrequest')->__('Created Time'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'created_time',
          'type'	  => 'datetime'
      ));
      $allmarket = Mage::helper('catalogrequest')->getMarketValueForExport();
      $this->addColumn('market',array(
      	  'header'    => Mage::helper('catalogrequest')->__('Market'),
      	  'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'market',
           'type'	  =>'options',
          'options'   => $allmarket,
      ));
   
 	
 	
   	
 	  $allHearabout = Mage::helper('catalogrequest')->getAllHearAboutForExport();
      $this->addColumn('hear_about', array(
          'header'    => Mage::helper('catalogrequest')->__('HearAbout'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'hear_about',
          'type'	  =>'options',
          'options'   => $allHearabout,
          
      ));  
      $this->addColumn('prospect', array(
          'header'    => Mage::helper('catalogrequest')->__('Prospect'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => ('prospect'?'prospect':'Y'), 
      )); 
       $this->addColumn('catalog_code', array(
          'header'    => Mage::helper('catalogrequest')->__('Catalog Code'),
          'header_export'=>' ',
          'align'     =>'left',
          'index'     => 'catalog_code',
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