<?php

class Krishinc_Customcontact_Block_Adminhtml_Customcontact_Customgrid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
     
      $this->setId('customcontactGrid');
      $this->setDefaultSort('customcontact_id');
      $this->setDefaultDir('ASC');
      
      $this->setSaveParametersInSession(true);
      
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('customcontact/customcontact')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('customcontact_id', array(
          'header'    => Mage::helper('customcontact')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'customcontact_id',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('customcontact')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));
    $this->addColumn('email', array(
          'header'    => Mage::helper('customcontact')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
    $this->addColumn('region', array(
          'header'    => Mage::helper('customcontact')->__('State'),
          'align'     =>'left',
          'index'     => 'region'
          
      ));
    $this->addColumn('subject', array(
          'header'    => Mage::helper('customcontact')->__('Subject'),
          'align'     =>'left',
          'index'     => 'subject'
          
      ));
      
       $this->addColumn('foundvia', array(
          'header'    => Mage::helper('customcontact')->__('How did you find our website?'),
          'align'     =>'left',
          'index'     => 'foundvia'
          
      ));
       $this->addColumn('used_products', array(
          'header'    => Mage::helper('customcontact')->__('Have you ever used our products?'),
          'align'     =>'left',
          'index'     => 'used_products' 
      ));
 
 
       $this->addColumn('comment', array(
          'header'    => Mage::helper('customcontact')->__('Comment'),
          'align'     =>'left',
          'index'     => 'comment'
          
      ));
 
    $this->addColumn('created_time', array(
          'header'    => Mage::helper('customcontact')->__('Created Time'),
          'align'     =>'left',
          'index'     => 'created_time',
          'type'	  => 'datetime'
      ));
  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customcontact')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customcontact')->__('View'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('customcontact')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('customcontact')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customcontact_id');
        $this->getMassactionBlock()->setFormFieldName('customcontact');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('customcontact')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('customcontact')->__('Are you sure?')
        )); 
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/view', array('id' => $row->getId()));
  }

}