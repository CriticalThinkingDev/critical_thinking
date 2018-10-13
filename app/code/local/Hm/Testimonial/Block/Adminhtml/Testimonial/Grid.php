<?php

class Hm_Testimonial_Block_Adminhtml_Testimonial_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('testimonialGrid');
      $this->setDefaultSort('testimonial_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('testimonial/testimonial')->getCollection();
	  	$store = $this->_getStore();
		if ($store->getId()) {
	            $collection->addStoreFilter($store);
		}      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

	protected function _getStore()
	{
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}  
  
  protected function _prepareColumns()
  {
      $this->addColumn('testimonial_id', array(
          'header'    => Mage::helper('testimonial')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'testimonial_id',
      ));

      $this->addColumn('client_name', array(
          'header'    => Mage::helper('testimonial')->__('Client Name'),
          'align'     =>'left',
          'index'     => 'client_name',
		  'width'	=> '200px',
      )); 

      $this->addColumn('address', array(
          'header'    => Mage::helper('testimonial')->__('State'),
          'align'     =>'left',
          'index'     => 'address',
           'width'    => '150px',
      ));
      
      $this->addColumn('media', array(
          'header'    => Mage::helper('testimonial')->__('Media'),
          'align'     =>'left',
          'index'     => 'media',
          'renderer'  => 'testimonial/adminhtml_testimonial_media',
          'width'    => '100px',
      ));

  $this->addColumn('product_name', array(
          'header'    => Mage::helper('testimonial')->__('Product Name'),
          'align'     =>'left',
          'index'     => 'product_name',
          'width'	=> '200px',
      ));
      
      $this->addColumn('sort_order', array(
          'header'    => Mage::helper('testimonial')->__('Sort Order'),
          'align'     =>'left',
          'index'     => 'sort_order',
	 	  'width'    => '100px',
      ));
      
   /*   $this->addColumn('email', array(
          'header'    => Mage::helper('testimonial')->__('Client Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));  */    

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('testimonial')->__('Testimonial Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */
	 $this->addColumn('is_teacher', array(
          'header'    => Mage::helper('testimonial')->__('Is Teacher?'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'is_teacher',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              2 => 'No',
          ),
      ));
      $this->addColumn('status', array(
          'header'    => Mage::helper('testimonial')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('testimonial')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('testimonial')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('testimonial')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('testimonial')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('testimonial_id');
        $this->getMassactionBlock()->setFormFieldName('testimonial');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('testimonial')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('testimonial')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('testimonial/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('testimonial')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('testimonial')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
